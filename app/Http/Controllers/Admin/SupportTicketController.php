<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TicketMail;
use App\Models\Contact;
use App\Models\Message;
use App\Models\Setting;
use App\Models\SupportTicket;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SupportTicketController extends Controller
{
    protected $ticket;
    public $user;

    public function __construct(SupportTicket $ticket)
    {
        $this->ticket     = $ticket;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public function index()
    {
        // if (is_null($this->user) || !$this->user->can('admin.support-ticket.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $title = 'Support Ticket';
        $tickets = SupportTicket::latest()->get();
        return view('admin.support_ticket.index', compact('title', 'tickets'));
    }

    public function view($id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.support-ticket.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $ticket = SupportTicket::find($id);
        $html = view('admin.support_ticket.view', compact('ticket'))->render();
        return response()->json($html);
    }

    public function update(Request $request, $id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.support-ticket.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $ticket = SupportTicket::find($id);
        $ticket->status = $request->status;
        $ticket->save();
        Toastr::success('Ticket status updated successfully');
        return redirect()->back();
    }

    public function delete($id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.support-ticket.delete')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $ticket = SupportTicket::find($id);
        $ticket->delete();
        Toastr::success('Ticket deleted successfully');
        return redirect()->back();
    }

    public function ticketShow($id)
    {

        // if (is_null($this->user) || !$this->user->can('admin.support-ticket.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        // $data['title'] = 'Ticket Reply';

        // $data['ticket'] = Ticket::where('id', $id)->with('messages','user')->first();

        // abort_if(is_null($data), 404);

        // Ticket::find($id)->update([
        //     'admin_seen' => 1
        // ]);

        // Message::where('support_ticket_id', $id)->where('msg_from', 1)->update([
        //     'is_seen' => 1
        // ]);



        $title  = "Ticket-View";
        $data = SupportTicket::where('id', $id)->with('messages')->first();

        abort_if(is_null($data), 404);

        $supportTicket = SupportTicket::find($id);
        if ($supportTicket->status === 0) {
            $supportTicket->update([
                'status' => 1,
                'admin_seen' => 1,
            ]);
        } else {
            $supportTicket->update([
                'admin_seen' => 1,
            ]);
        }

        Message::where('support_ticket_id', $id)->where('msg_from', 2)->update([
            'is_seen' => 1
        ]);

        return view('admin.support_ticket.show', compact('data', 'id', 'title'));
    }


    public function reply(Request $request, $id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.support-ticket.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        // verifying ticket and user
        abort_if(is_null(SupportTicket::where('id', $id)->first()), 404);

        $user = SupportTicket::where('id', $id)->with('user')->first()->user;

        $message = new Message();

        $request->validate([
            'message' => 'required',
        ]);

        $message->support_ticket_id            = $id;
        $message->message                      = $request->message;
        $message->created_at                   = now();
        $message->created_by                   = auth('admin')->id();
        $message->msg_from                     = 2;
        $message->is_seen                      = 0;


        // attachment

        $attachemts = [];

        if ($request->hasFile('attachment')) {

            foreach ($request->file('attachment') as $key => $attachment) {
                # code...

                if ($attachment->isValid()) {
                    $name = $attachment->getClientOriginalName();
                    $path = "uploads".DIRECTORY_SEPARATOR."ticket-message".DIRECTORY_SEPARATOR;
                    $imgName = (string) \Str::uuid() . "." . $attachment->getClientOriginalExtension();
                    $uploadPath = public_path($path);
                    $attachment->move($uploadPath, $imgName);

                    $attachemts[$name] = $path . $imgName;
                }
            }
        }

        $message->attachment     =   json_encode($attachemts);

        $message = $message->load('sender','ticket.user');

        $userMsg = 'Admin is being replied to your ticket: '. $request->message;
        // Mail::to($email)->send(new TicketMail($userMsg));

        if (!$message->save()) {
            Toastr::error(trans('Failed to add message for this ticket'), 'Failed', ["positionClass" => "toast-top-center"]);
            return back();
        } else {
            $setting = Setting::first();
            $body = "Admin has replied to your support ticket #{$message->support_ticket_id}. Please check the ticket for further details.";

            $data = [
                'greeting' => 'Hi, '.$user->name.' '.$user->last_name,
                'body' => $body,
                'msg' => $request->message,
                'link' => "Click here to view to the message: <a href='" . route('user.ticket.show', $message->support_ticket_id) . "'>View Ticket</a>",
                'site_name' => $setting->site_name ?? config('app.name'),
                'site_url' => url('/'),
                'footer' => 1,
            ];
    
            try {
                Mail::to($user->email)->send(new TicketMail($data));
            } catch (\Exception $e) {
                Log::alert('Support mail not sent. Error: ' . $e->getMessage());
            }
            Toastr::success(trans('Reply Successfully'), 'Success', ["positionClass" => "toast-top-center"]);
            return back();
        }
    }
}
