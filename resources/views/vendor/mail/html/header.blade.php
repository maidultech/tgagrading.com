@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="{{ config('app.url') }}/assets/order_email_logo.png" alt="{{config('app.name')}}" style="width: 150px; height: 50px;">
@else
<img src="{{ config('app.url') }}/assets/order_email_logo.png" alt="{{config('app.name')}}" style="width: 150px; height: 50px;">
@endif
</a>
</td>
</tr>
