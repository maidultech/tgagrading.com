<?php

namespace App\Http\Controllers\User;

use App\Models\SupportTicket;
use App\Models\Ticket;
use App\Models\Message;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Mail\TicketMail;
use App\Models\Setting;
use App\Services\ZendeskService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Climate\Supplier;

class TicketController extends Controller
{

    protected $zendeskService;
    public $priority = ['low', 'medium', 'high'];
    public $status = [1 => 'Open', 5 =>  'Closed', 8 => 'ReOpened'];
    
    public function __construct(ZendeskService $zendeskService)
    {
        $this->zendeskService = $zendeskService;
    }

    public function index()
    {
        Paginator::useBootstrap();
        $user = Auth::user();
        // $tickets = SupportTicket::where('user_id', auth()->id())->paginate(10);

        try {
            $data = $this->zendeskService->getTicketsByUserEmail($user->email); 
            $tickets = $data['tickets'];
        } catch (\Exception $e) {
            $tickets = [];
        }
  
        $priority = $this->priority;
        $status = $this->status;
        $title = "Support-Ticket";

        return view('user.ticket.index', compact('tickets', 'priority', 'status', 'title'));
    }

    public function create()
    {

        $data['title']  = "Ticket-Create";
        return view('user.ticket.create',$data);
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'subject' => 'required|string|max:500',
            'message' => 'required|string|max:1200',
        ]);

        $attachments = [];
        if ($request->hasFile('attachment')) {
            $attachments = $request->file('attachment');
        }
        
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $zendeskTicket = $this->zendeskService->createTicketWithAttachments(
                $request->subject,
                $request->message,
                [
                    'name' => $user->name . ' ' . $user->last_name,
                    'email' => $user->email,
                ],
                $attachments
            );

            $ticket                     = new SupportTicket();
            $ticket->user_id            = $user->id;
            $ticket->subject            = $request->subject;
            $ticket->status             = 0;
            $ticket->ticket_number      = SupportTicket::max('ticket_number') + 1;
            $ticket->admin_seen         = 0;
            $ticket->created_at         = now();
            $ticket->save();


            // saving message
            $message = new Message();
            $message->support_ticket_id = $ticket->id;
            $message->message           = $request->message;
            $message->created_at        = now();
            $message->created_by        = $ticket->user_id;
            $message->msg_from          = 1;
            $message->is_seen           = 0;
            $ticket->save();

            $attachments = [];

            if ($request->hasFile('attachment')) {
                foreach ($request->file('attachment') as $key => $attachment) {
                    if ($attachment->isValid()) {
                        $name = $attachment->getClientOriginalName();
                        $path = 'uploads/ticket-message/';
                        $imgName = (string) Str::uuid() . "." . $attachment->extension();
                        $uploadPath = public_path($path);
                        $attachment->move($uploadPath, $imgName);

                        $attachments[$name] = $path . $imgName;
                    }
                }
            }

            $message->attachment  = json_encode($attachments);
            $message->save();
            DB::commit();
            Toastr::success('Ticket created successfully', 'Success', ["positionClass" => "toast-top-center"]);
            return redirect(route('user.ticket.index'));

        } catch (\Exception $e) {
            // dd($e);
            DB::rollBack();
            Toastr::error($e->getMessage(), 'Failed', ["positionClass" => "toast-top-center"]);
            return redirect(route('user.ticket.create'));
        }

    }

    // public function store(Request $request)
    // {
    //     $user = Auth::user();
    //     DB::beginTransaction();
    //     try {
    //         //code...
    //         $request->validate([
    //             'subject' => 'required|string|max:500',
    //             'priority' => 'required|in:1,2,3',
    //             'message' => 'required|string|max:1200',
    //         ]);

    //         // creating ticket

    //         $ticket                     = new SupportTicket();

    //         $ticket->user_id            = $user->id;

    //         $ticket->subject            = $request->subject;
    //         $ticket->status             = 0;
    //         $ticket->priority           = $request->priority;
    //         $ticket->ticket_number      = SupportTicket::max('ticket_number') + 1;

    //         $ticket->admin_seen         = 0;
    //         $ticket->created_at         = now();

    //         if (!$ticket->save()) {
    //             Toastr::error(trans('Failed to create ticket'), 'Failed', ["positionClass" => "toast-top-center"]);
    //             return back();
    //         }

    //         // saving message
    //         $message = new Message();

    //         $message->support_ticket_id            = $ticket->id;
    //         $message->message                      = $request->message;
    //         $message->created_at                   = now();
    //         $message->created_by                   = $ticket->user_id;
    //         $message->msg_from                     = 1;
    //         $message->is_seen                      = 0;

    //         // attachment
    //         $attachemts = [];

    //         if ($request->hasFile('attachment')) {
    //             //  and $request->file('attachment')->isValid()

    //             foreach ($request->file('attachment') as $key => $attachment) {
    //                 # code...
    //                 if ($attachment->isValid()) {
    //                     $name = $attachment->getClientOriginalName();
    //                     $path = 'uploads/ticket-message/';
    //                     $imgName = (string) Str::uuid() . "." . $attachment->extension();
    //                     $uploadPath = public_path($path);
    //                     $attachment->move($uploadPath, $imgName);

    //                     $attachemts[$name] = $path . $imgName;
    //                 }
    //             }
    //         }

    //         $message->attachment                      = json_encode($attachemts);

    //         if (!$message->save()) {
    //             Toastr::error(trans('Failed to add message for this ticket'), 'Failed', ["positionClass" => "toast-top-center"]);
    //             return back();
    //         } else {

    //             $priority = match ($ticket->priority) {
    //                 1 => 'Low',
    //                 2 => 'Medium',
    //                 3 => 'High',
    //                 default => 'Low',
    //             };

    //             $setting = Setting::first();
    //             $body = 'A new support ticket has been created. Please review and respond at your earliest convenience.';
    //             $data = [
    //                 'greeting' => 'Hi Admin,',
    //                 'body' => $body,
    //                 'name' => $user->name.' '.$user->last_name,
    //                 'email' => $user->email,
    //                 'subject' => $request->subject,
    //                 'msg' => $request->message,
    //                 'priority' => $priority,
    //                 'site_name' => $setting->site_name,
    //                 'site_url' => url('/'),
    //                 'footer' => 1,
    //             ];
        
    //             try {
    //                 Mail::to($setting->email)->send(new TicketMail($data));
    //             } catch (\Exception $e) {
    //                 Log::alert('Support mail not sent. Error: ' . $e->getMessage());
    //             }
                
    //             DB::commit();
    //             Toastr::success(trans('Ticket Created Successfully'), 'Success', ["positionClass" => "toast-top-center"]);
    //             return redirect(route('user.ticket.show', ['id' => $ticket->id]));
    //         }
    //     } catch (\Throwable $th) {
    //         throw $th;

    //         DB::rollBack();

    //         if(isset($th->validator)){
    //             $request->flash();
    //             $msg = implode(', ', \Arr::Flatten($th->validator->messages()->get('*')));
    //             Toastr::error(trans($msg), 'Failed', ["positionClass" => "toast-top-center"]);
    //             return redirect()->back();
    //         }
    //         Toastr::error(trans('Failed to Create Ticket'), 'Failed', ["positionClass" => "toast-top-center"]);
    //         return redirect(route('user.ticket.index'));
    //     }
    // }

    // public function reply(Request $request, $id)
    // {
    //     // verifying ticket and user
    //     abort_if(is_null(SupportTicket::where('id', $id)->where('user_id', auth()->id())->first()), 404);

    //     $message = new Message();

    //     $request->validate([
    //         'message' => 'required',
    //     ]);

    //     $message->support_ticket_id            = $id;
    //     $message->message                      = $request->message;
    //     $message->created_at                   = now();
    //     $message->created_by                   = auth()->id();
    //     $message->msg_from                     = 1;
    //     $message->is_seen                      = 0;


    //     // attachment

    //     $attachemts = [];

    //     // dd($request->hasFile('attachment'));

    //     if ($request->hasFile('attachment')) {


    //         foreach ($request->file('attachment') as $key => $attachment) {
    //             # code...

    //             if ($attachment->isValid()) {
    //                 $name = $attachment->getClientOriginalName();
    //                 $path = "uploads".DIRECTORY_SEPARATOR."ticket-message".DIRECTORY_SEPARATOR;
    //                 $imgName = (string) Str::uuid() . "." . $attachment->getClientOriginalExtension();
    //                 $uploadPath = public_path($path);
    //                 $attachment->move($uploadPath, $imgName);

    //                 $attachemts[$name] = $path . $imgName;
    //             }
    //         }
    //     }

    //     $message->attachment                      = json_encode($attachemts);


    //     if (!$message->save()) {
    //         Toastr::error(trans('Failed to add message for this ticket'), 'Failed', ["positionClass" => "toast-top-center"]);
    //         return back();
    //     } else {
    //         $user = Auth::user();
    //         $setting = Setting::first();
    //         $body = "User from Ticket #{$message->support_ticket_id} has sent a reply. Please respond at your earliest convenience.";

    //         $data = [
    //             'greeting' => 'Hi Admin,',
    //             'body' => $body,
    //             'ticket_id' => $message->support_ticket_id,
    //             'msg' => $request->message,
    //             'link' => "Click here to reply to the message: <a href='" . route('admin.support-ticket.show', $message->support_ticket_id) . "'>View Ticket</a>",
    //             'site_name' => $setting->site_name ?? config('app.name'),
    //             'site_url' => url('/'),
    //             'footer' => 1,
    //         ];
    
    //         try {
    //             Mail::to($setting->email)->send(new TicketMail($data));
    //         } catch (\Exception $e) {
    //             Log::alert('Support mail not sent. Error: ' . $e->getMessage());
    //         }

    //         Toastr::success(trans('Reply added Successfully'), 'Success', ["positionClass" => "toast-top-center"]);
    //         return back();;
    //     }
    // }

    // public function ticketShow($id)
    // {
    //     $title  = "Ticket-View";
    //     $data = SupportTicket::where('id', $id)->where('user_id', auth()->id())->with('messages')->first();

    //     abort_if(is_null($data), 404);

    //     $main = Message::where('support_ticket_id', $id)->with('sender')->where('msg_from',2)->update([
    //         'is_seen' => 1
    //     ]);

    //     return view('user.ticket.show', compact('data', 'id', 'title'));

    // }

    public function reply(Request $request, $id, $author_id)
    {
        $request->validate([
            'message' => 'required|string|max:1200',
        ]);
    
        try {
            $attachments = $request->file('attachment') ?? [];
            $zendeskService = new ZendeskService();
    
            $zendeskService->replyToTicket($id, $author_id, $request->message, $attachments);
    
            Toastr::success('Reply sent successfully', 'Success', ["positionClass" => "toast-top-center"]);
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error($e->getMessage(), 'Error', ["positionClass" => "toast-top-center"]);
            return redirect()->back();
        }
    }

    public function ticketShow($id)
    {
        try {
            $zendeskService = new ZendeskService();
            $data = $zendeskService->getTicketDetailsWithComments($id);
            $user = auth()->user();
            return view('user.ticket.show', [
                'ticket' => $data['ticket'],
                'messages' => $data['comments'],
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            Toastr::error($e->getMessage(), 'Error', ["positionClass" => "toast-top-center"]);
            return redirect(route('user.ticket.index'));
        }
    }
}
