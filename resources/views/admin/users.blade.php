@extends('layouts.admin')

@section('content')
@php
    $users = \App\Models\User::latest()->get();
    $roleCounts = $users->groupBy('role')->map->count();
    $providersByEmail = $providers->keyBy('email');
@endphp

    <h1>Users</h1>
    <div class="m">Everyone with an account on MrRel.</div>

    <div class="row" style="margin:16px 0;flex-wrap:wrap;">
        <span class="chip">All ({{ $users->count() }})</span>
        @foreach($roleCounts as $role => $count)
            <span class="chip">{{ $role ?: 'No role' }} ({{ $count }})</span>
        @endforeach
    </div>

    <div class="card">
        <h3>Accounts</h3>
        @if($users->isEmpty())
            <p class="m">No accounts yet. Users appear here after they register.</p>
        @else
            <div style="overflow-x:auto;">
            <table class="tb">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Action</th>
                </tr>
                @foreach($users as $user)
                    @php $provider = $user->role === 'Provider' ? $providersByEmail->get($user->email) : null; @endphp
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role }}</td>
                        <td>
                            @if($user->role === 'Provider')
                                @if(!$provider)
                                    <span class="chip">No provider profile yet</span>
                                @elseif($provider->verification_status === 'Verified')
                                    <span class="chip ok">Verified</span>
                                @else
                                    <span class="chip wn">Waiting for verification</span>
                                @endif
                            @else
                                <span class="chip ok">Active</span>
                            @endif
                        </td>
                        <td class="m sm">{{ optional($user->created_at)->format('d M Y') }}</td>
                        <td>
                            @if($provider && $provider->verification_status !== 'Verified')
                                <form method="POST" action="{{ route('admin.providers.verify', $provider->id) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn" style="padding:4px 12px;font-size:12px;margin:0 4px 0 0;">Verify</button>
                                </form>
                                <a href="{{ route('admin.providers') }}" class="btn g" style="padding:4px 12px;font-size:12px;margin:0;text-decoration:none;">See documents</a>
                            @endif
                            @if($user->role !== 'Admin')
                                <form method="POST" action="{{ route('admin.users.delete', $user->id) }}" style="display:inline;" onsubmit="return confirm({{ \Illuminate\Support\Js::from('Remove '.$user->name.' and everything linked to them? This cannot be undone.') }});">
                                    @csrf
                                    <button type="submit" class="btn g" style="padding:4px 12px;font-size:12px;margin:0;color:#8A1C1C;">Remove</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
            </div>
        @endif
    </div>
@endsection