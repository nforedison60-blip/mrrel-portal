@extends('layouts.landlord')

@section('content')
    <h1>Viewing requests</h1>
    <div class="m">Tenant details are shared only after a tenant asks for a viewing.</div>

    <div class="card" style="margin-top: 20px;">
        <h3>Requests for your properties</h3>
        @if($viewingRequests->isEmpty())
            <div class="empty">
                <h3>No viewing requests yet</h3>
                <p class="m">When a tenant asks to view one of your live properties, the request appears here.</p>
                <a href="{{ route('landlord.properties') }}" class="btn g" style="text-decoration:none;">See my properties</a>
            </div>
        @else
            <div style="overflow-x:auto;">
            <table class="tb">
                <tr>
                    <th>Tenant</th>
                    <th>Property</th>
                    <th>Message</th>
                    <th>Requested</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                @foreach($viewingRequests as $req)
                    <tr>
                        <td>{{ $req->customer_name }}</td>
                        <td>{{ optional($req->property)->title ?? 'Property no longer available' }}</td>
                        <td>{{ $req->message ?: 'No message' }}</td>
                        <td class="m sm">{{ $req->created_at->format('d M Y') }}</td>
                        <td><span class="chip {{ $req->status === 'Completed' ? 'ok' : 'wn' }}">{{ $req->status === 'Completed' ? 'Done' : 'Waiting' }}</span>
                            @if($req->mrrel_attends)<span class="chip ok">MrRel will attend</span>@endif</td>
                        <td>
                            @if($req->status !== 'Completed')
                                <form method="POST" action="{{ route('landlord.viewing.resolve', $req->id) }}" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="btn" style="padding: 6px 14px; font-size: 13px; margin:0;">Mark as done</button>
                                </form>
                            @else
                                <span class="m sm">Done</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
            </div>
            <p class="m sm" style="margin-top: 16px;">MrRel arranges viewings with you. Mark a request as done once the viewing has happened. The tenant then sees it as completed.</p>
        @endif
    </div>
@endsection
