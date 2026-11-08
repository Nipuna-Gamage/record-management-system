@extends('layouts.app')
@section('title', 'Notifications')
@section('content')
<div class="page-heading">
    <p class="eyebrow">UPDATES & REMINDERS</p>
    <div class="heading-row"><div><h1>Notifications</h1><p class="muted">You have {{ $unreadCount }} unread notification(s).</p></div>
        @if($unreadCount > 0)<form method="POST" action="{{ route('notifications.read-all') }}">@csrf @method('PATCH')<button class="btn light" type="submit">Mark all as read</button></form>@endif
    </div>
</div>
<div class="content-panel">
    @forelse($notifications as $notification)
        <div class="notification-item" style="display:flex;justify-content:space-between;gap:16px;align-items:flex-start;padding:16px 0;border-bottom:1px solid #e8edf3;{{ $notification->read_at ? 'opacity:.72' : '' }}">
            <div style="display:flex;gap:12px;align-items:flex-start">
                <span aria-hidden="true" style="font-size:20px">{{ ($notification->data['kind'] ?? '') === 'record_due' ? '⏰' : '📄' }}</span>
                <div><strong>{{ $notification->data['title'] ?? 'Notification' }}</strong><p class="muted" style="margin:5px 0">{{ $notification->data['message'] ?? '' }}</p><small class="muted">{{ $notification->created_at->format('d M Y, H:i') }} · {{ $notification->read_at ? 'Read' : 'Unread' }}</small></div>
            </div>
            @if(!$notification->read_at)<form method="POST" action="{{ route('notifications.read', $notification->id) }}">@csrf<button class="btn light" type="submit">Open</button></form>@elseif(!empty($notification->data['url']))<a class="btn light" href="{{ $notification->data['url'] }}">View</a>@endif
        </div>
    @empty
        <p class="muted">You are all caught up. No notifications yet.</p>
    @endforelse
    <div style="margin-top:18px">{{ $notifications->links() }}</div>
</div>
@endsection
