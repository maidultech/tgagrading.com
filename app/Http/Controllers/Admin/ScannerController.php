<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderCard;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderMail;
use App\Models\ManualLabel;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class ScannerController extends Controller
{
    public function scan(Request $request, Order $order)
    {

        $request->validate([
            'page_type' => 'required|in:front_page,back_page',
            'card_id' => 'required|exists:order_cards,id',
            'image' => 'required',
        ]);

        try {
            $pageType = $request->page_type;
            $card = OrderCard::findOrFail($request->card_id);

            if ($request->has('image')) {
                $base64Image = $request->image;
        
                // Convert Base64 to an Uploaded File
                $imageFile = $this->convertBase64ToFile($base64Image, 'scanned_image.png');
                // Upload scanned image using your custom function
                $imagePath = uploadGeneralImage($imageFile, 'card');
            }

            // Update OrderCard with the correct image path
            if ($pageType == 'front_page') {
                $card->front_page = $imagePath;
            } else {
                $card->back_page = $imagePath;
            }

            $card->save();

            // Order status update logic
            if ($order->status >= 20 && $order->status <= 30) {
                $totalCards = $order->cards->where('is_no_grade', 0)->count();
                $completedCards = $order->cards->whereNotNull('front_page')->count();

                if ($totalCards == $completedCards) {
                    if ($order->status != 30) {
                        $this->sendOrderUpdate($order, 30, "We have now finished encapsulating your cards. We will update you once your order is ready to be shipped!");
                        $order->status = 30;
                    }
                } else {
                    if ($order->status != 25) {
                        $this->sendOrderUpdate($order, 25, "We have started slabbing your cards and will update you once we are complete!");
                        $order->status = 25;
                    }
                }
            }

            $order->save();
            session()->forget(['page_type', 'card_id', 'order_id', 'is_manual']);

        } catch (\Exception $e) {
            // dd($e);
            Toastr::error('Error uploading image', 'Error');
            return back();
        }

        Toastr::success('Image uploaded successfully', 'Success');
        return redirect()->route('admin.order.certificate.index', $order->id)
        ->with([
            'scrollToIndex' => $request->scrollToIndex
        ]);
    
    }

    public function manualLabelScan(Request $request)
    {
        $request->validate([
            'page_type' => 'required|in:front_page,back_page',
            'card_id' => 'required|exists:manual_labels,id',
            'image' => 'required',
        ]);

        try {
            $pageType = $request->page_type;
            $card = ManualLabel::findOrFail($request->card_id);

            if ($request->has('image')) {
                $base64Image = $request->image;
        
                // Convert Base64 to an Uploaded File
                $imageFile = $this->convertBase64ToFile($base64Image, 'scanned_image.png');
                // Upload scanned image using your custom function
                $imagePath = uploadGeneralImage($imageFile, 'card');
            }

            // Update OrderCard with the correct image path
            if ($pageType == 'front_page') {
                $card->front_page = $imagePath;
            } else {
                $card->back_page = $imagePath;
            }

            $card->save();
            session()->forget(['page_type', 'card_id', 'order_id', 'is_manual']);

        } catch (\Exception $e) {
            // dd($e);
            Toastr::error('Error uploading image', 'Error');
            return back();
        }

        Toastr::success('Image uploaded successfully', 'Success');
        return redirect()->route('admin.manual-label.index');
    }
    // Helper function to send order updates
    private function sendOrderUpdate($order, $status, $message)
    {
        $statusConfig = config('static_array.status');
        $setting = getSetting();
        $body = "The status of your order {$order->order_number} has been updated.";

        $data = [
            'subject' => 'Order Update From ' . $setting->site_name . ': ' . $statusConfig[$status],
            'greeting' => 'Hi, ' . $order->rUser?->name . ' ' . $order->rUser?->last_name,
            'body' => $body,
            'order_number' => $order->order_number,
            'status' => $statusConfig[$status],
            'site_name' => $setting->site_name ?? config('app.name'),
            'thanks' => $message,
            'site_url' => url('/'),
            'footer' => 1,
        ];

        try {
            Mail::to($order->rUser?->email)->send(new OrderMail($data));
        } catch (\Exception $e) {
            Log::alert('Order mail not sent. Error: ' . $e->getMessage());
        }
    }

    /**
     * Convert Base64 string to an UploadedFile instance
     */
    private function convertBase64ToFile($base64String, $fileName, $path = 'images')
    {
        // Remove metadata if present (e.g., "data:image/png;base64,")
        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $matches)) {
            $base64String = substr($base64String, strpos($base64String, ',') + 1);
            $extension = $matches[1]; // Get file extension (e.g., png, jpg)
        } else {
            $extension = 'png'; // Default extension if not found
        }
    
        // Decode Base64
        $imageData = base64_decode($base64String);

        // Define the public upload path
        $directory = public_path("uploads/$path");
    
        // Ensure the directory exists
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }
    
        // Define the full file path
        $filePath = $directory . '/' . $fileName;
    
        // Save the file
        file_put_contents($filePath, $imageData);
    
        // Convert to UploadedFile instance
        return new UploadedFile(
            $filePath,
            $fileName,
            'image/' . $extension,
            null,
            true // Mark as test file
        );
    }

    public function uploadChunk(Order $order, Request $request) 
    {
        try {
            $request->validate([
                'chunk' => 'required',
                'chunk_index' => 'required|integer',
                'total_chunks' => 'required|integer',
                'page_type' => 'required|in:front_page,back_page',
                'card_id' => 'required|exists:order_cards,id',
            ]);
    
            $card = OrderCard::findOrFail($request->card_id);
            $tempDir = public_path("uploads/temp_{$request->card_id}");
            if (!File::isDirectory($tempDir)) {
                File::makeDirectory($tempDir, 0777, true, true);
            }
    
            // Save chunk
            $chunkPath = $tempDir . "/chunk_{$request->chunk_index}.txt";
            File::put($chunkPath, $request->chunk);
    
            // If all chunks are received, reconstruct the file
            if ($request->chunk_index + 1 == $request->total_chunks) {
                $finalBase64 = '';
                for ($i = 0; $i < $request->total_chunks; $i++) {
                    $finalBase64 .= File::get("$tempDir/chunk_$i.txt");
                }
    
                $imageFile = $this->convertBase64ToFile($finalBase64, 'scanned_image.png');
                $imagePath = uploadGeneralImage($imageFile, 'card');
    
                // Assign the correct field in the OrderCard model
                if ($request->page_type == 'front_page') {
                    $card->front_page = $imagePath;
                } else {
                    $card->back_page = $imagePath;
                }
    
                $card->save();
    
                // Update order status if needed
                if ($order->status >= 20 && $order->status <= 30) {
                    $totalCards = $order->cards->where('is_no_grade', 0)->count();
                    $completedCards = $order->cards->whereNotNull('front_page')->count();
    
                    if ($totalCards == $completedCards) {
                        if ($order->status != 30) {
                            $this->sendOrderUpdate($order, 30, "We have now finished encapsulating your cards. We will update you once your order is ready to be shipped!");
                            $order->status = 30;
                        }
                    } else {
                        if ($order->status != 25) {
                            $this->sendOrderUpdate($order, 25, "We have started slabbing your cards and will update you once we are complete!");
                            $order->status = 25;
                        }
                    }
                }
    
                $order->save();
                session()->forget(['page_type', 'card_id', 'order_id', 'is_manual']);
                session()->flash('scrollToIndex', $request->scroll_index);
                File::deleteDirectory($tempDir);
                return response()->json(['message' => 'Upload complete', 'path' => $imagePath], 200);
            }
    
            return response()->json(['message' => 'Chunk received'], 200);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while uploading.'], 500);
        }
    }

    public function manualUploadChunk(Order $order, Request $request) 
    {

        try {
            $request->validate([
                'chunk' => 'required',
                'chunk_index' => 'required|integer',
                'total_chunks' => 'required|integer',
                'page_type' => 'required|in:front_page,back_page',
                'card_id' => 'required|exists:manual_labels,id',
            ]);

            $card = ManualLabel::findOrFail($request->card_id);
            $tempDir = public_path("uploads/temp_{$request->card_id}");
            if (!File::isDirectory($tempDir)) {
                File::makeDirectory($tempDir, 0777, true, true);
            }
    
            // Save chunk
            $chunkPath = $tempDir . "/chunk_{$request->chunk_index}.txt";
            File::put($chunkPath, $request->chunk);
    
            // If all chunks are received, reconstruct the file
            if ($request->chunk_index + 1 == $request->total_chunks) {
                $finalBase64 = '';
                for ($i = 0; $i < $request->total_chunks; $i++) {
                    $finalBase64 .= File::get("$tempDir/chunk_$i.txt");
                }
    
                $imageFile = $this->convertBase64ToFile($finalBase64, 'scanned_image.png');
                $imagePath = uploadGeneralImage($imageFile, 'card');
    
                // Assign the correct field in the OrderCard model
                if ($request->page_type == 'front_page') {
                    $card->front_page = $imagePath;
                } else {
                    $card->back_page = $imagePath;
                }
    
                $card->save();
    
                session()->forget(['page_type', 'card_id', 'order_id', 'is_manual']);
                File::deleteDirectory($tempDir);
                return response()->json(['message' => 'Upload complete', 'path' => $imagePath], 200);
            }
    
            return response()->json(['message' => 'Chunk received'], 200);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while uploading.'], 500);
        }
    }
}