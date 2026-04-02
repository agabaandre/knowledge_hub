@extends('emails.layout')

@section('content')
<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <p>Hello {{ $recipientName }},</p>
    <p>A content request has been referred to your community. A <strong>dedicated forum thread</strong> (visible to your community) has been opened—use the link below after signing in. You are subscribed to the thread so you can comment without a separate “join” step. The requester is emailed when new forum comments are posted.</p>

    <div style="background-color: #f8f9fa; padding: 15px; margin: 20px 0; border-radius: 4px;">
        <p style="margin: 0 0 8px 0;"><strong>Community:</strong> {{ $contentRequest->referredToCommunity->community_name ?? 'Your community' }}</p>
        <p style="margin: 0 0 8px 0;"><strong>Request subject:</strong> {{ $contentRequest->subject }}</p>
        @if($contentRequest->referral_notes)
            <p style="margin: 0; white-space: pre-wrap;"><strong>Notes from admin:</strong><br>{{ strip_tags($contentRequest->referral_notes) }}</p>
        @endif
    </div>

    <p style="margin: 24px 0;">
        <a href="{{ $discussUrl }}" style="display: inline-block; background-color: #119A48; color: #ffffff !important; padding: 12px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;">Open forum thread (sign in required)</a>
    </p>

    <p>Thank you,<br><strong>Africa CDC Knowledge Hub Team</strong></p>
</div>
@endsection
