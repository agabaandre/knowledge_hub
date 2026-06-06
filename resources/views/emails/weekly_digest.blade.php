@extends('emails.layout')

@section('content')
<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2 style="color: #119A48; margin-bottom: 20px;">Weekly Digest</h2>

    <p>Hello {{ $subscriber->name ?: 'Subscriber' }},</p>

    <p>Here’s what’s new on the Knowledge Hub from <strong>{{ $digest['sinceFormatted'] }}</strong> to <strong>{{ $digest['weekEndFormatted'] }}</strong>.</p>

    @if($digest['publications']->count() > 0)
    <div style="margin: 24px 0;">
        <h3 style="color: #119A48; margin-bottom: 12px; font-size: 1.1em;">New resources & publications</h3>
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px;">
            @foreach($digest['publications']->take(10) as $pub)
            <div style="padding: 10px 0; border-bottom: 1px solid #e2e8f0;">
                @if(!$loop->first)
                <div style="border-top: 1px solid #e2e8f0; margin-top: 8px; padding-top: 8px;"></div>
                @endif
                <a href="{{ publication_url($pub) }}" style="color: #119A48; font-weight: 600; text-decoration: none;">{{ \Illuminate\Support\Str::limit(strip_tags($pub->title ?? 'Untitled'), 80) }}</a>
                @if($pub->author)
                <p style="margin: 4px 0 0 0; color: #64748b; font-size: 0.9em;">{{ $pub->author->name ?? '' }}</p>
                @endif
            </div>
            @endforeach
            @if($digest['publications']->count() > 10)
            <p style="margin: 12px 0 0 0; font-size: 0.9em;">
                <a href="{{ url('records/search') }}" style="color: #119A48;">View all {{ $digest['publications']->count() }} new resources →</a>
            </p>
            @else
            <p style="margin: 12px 0 0 0; font-size: 0.9em;">
                <a href="{{ url('records/search') }}" style="color: #119A48;">Browse all resources →</a>
            </p>
            @endif
        </div>
    </div>
    @endif

    @if($digest['forums']->count() > 0)
    <div style="margin: 24px 0;">
        <h3 style="color: #119A48; margin-bottom: 12px; font-size: 1.1em;">New forum discussions</h3>
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px;">
            @foreach($digest['forums']->take(10) as $forum)
            <div style="padding: 10px 0;">
                @if(!$loop->first)
                <div style="border-top: 1px solid #e2e8f0; margin-top: 8px; padding-top: 8px;"></div>
                @endif
                <a href="{{ forum_thread_url($forum) }}" style="color: #119A48; font-weight: 600; text-decoration: none;">{{ \Illuminate\Support\Str::limit(strip_tags($forum->forum_title ?? 'Untitled'), 80) }}</a>
                <p style="margin: 4px 0 0 0; color: #64748b; font-size: 0.9em;">{{ $forum->user->name ?? 'Unknown' }}</p>
            </div>
            @endforeach
            <p style="margin: 12px 0 0 0; font-size: 0.9em;">
                <a href="{{ url('forums') }}" style="color: #119A48;">Browse forums →</a>
            </p>
        </div>
    </div>
    @endif

    @if($digest['newMembers']->count() > 0 || count($digest['communityForumIds']) > 0)
    <div style="margin: 24px 0;">
        <h3 style="color: #119A48; margin-bottom: 12px; font-size: 1.1em;">Community activity</h3>
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px;">
            @if($digest['newMembers']->count() > 0)
            <p style="margin: 0 0 8px 0; font-weight: 600; color: #334155;">New members joined communities</p>
            @foreach($digest['newMembers']->take(8) as $m)
            <p style="margin: 4px 0; font-size: 0.95em;">{{ $m->user->name ?? 'Member' }} joined <strong>{{ $m->community->community_name ?? 'Community' }}</strong></p>
            @endforeach
            @endif
            @if(count($digest['communityForumIds']) > 0)
            <p style="margin: 12px 0 4px 0; font-weight: 600; color: #334155;">New discussions in communities</p>
            <p style="margin: 0; font-size: 0.95em;">{{ count($digest['communityForumIds']) }} new forum discussion(s) were posted in communities. <a href="{{ url('forums') }}" style="color: #119A48;">View forums →</a></p>
            @endif
            <p style="margin: 12px 0 0 0; font-size: 0.9em;">
                <a href="{{ url('communities') }}" style="color: #119A48;">Explore communities →</a>
            </p>
        </div>
    </div>
    @endif

    @if($digest['publications']->count() === 0 && $digest['forums']->count() === 0 && $digest['newMembers']->count() === 0 && count($digest['communityForumIds']) === 0)
    <p style="color: #64748b;">No new resources, forums, or community activity this week. Check back soon.</p>
    <p style="margin-top: 16px;">
        <a href="{{ url('/') }}" style="color: #119A48; font-weight: 600;">Visit the Knowledge Hub →</a>
    </p>
    @endif

    <p style="margin-top: 28px; color: #666; font-size: 0.9em;">
        Best regards,<br>
        Africa CDC Knowledge Hub
    </p>

    <p style="margin-top: 24px; color: #94a3b8; font-size: 0.85em; border-top: 1px solid #e2e8f0; padding-top: 16px;">
        You are receiving this because you are subscribed to our mailing list.<br>
        <a href="{{ url('/unsubscribe?email=' . urlencode($subscriber->email)) }}" style="color: #119A48;">Unsubscribe</a>
    </p>
</div>
@endsection
