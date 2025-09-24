
<div class="card-body table-responsive p-1">
    <table class="table">
        <tr>
            <td class="border-top-0">Subject :</td>
            <td class="border-top-0">{{ $ticket->subject }}</td>
        </tr>
        <tr>
            <td>Priority :</td>
            <td>
                @if($ticket->priority == 1)
                    Low
                @elseif($ticket->priority == 2)
                    Medium
                @else
                    High
                @endif
            </td>
        </tr>
        <tr>
            <td>Status :</td>
            <td>
                @if($ticket->status == '0')
                    <span class="text-warning">Pending</span>
                @elseif($ticket->status == '1')
                    <span class="text-success">Open</span>
                @else
                    <span class="text-danger">Close</span>
                @endif
            </td>
        </tr>
        <tr>
            <td>Date :</td>
            <td>{{ date('M d, Y', strtotime($ticket->created_at)) }}</td>
        </tr>
        @if($ticket->message)
        <tr>
            <td>Message :</td>
            <td>{{ $ticket->message }}</td>
        </tr>
        @endif
    </table>
</div>

