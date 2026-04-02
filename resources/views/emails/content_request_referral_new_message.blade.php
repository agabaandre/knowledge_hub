@extends('emails.layout')

@section('content')
<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <p>Dear colleague,</p>
    <p>There is a new message regarding your content request:</p>

    <div style="background-color: #e8f5e9; border-left: 4px solid #119A48; padding: 15px; margin: 20px 0;">
        <p style="margin: 0 0 8px 0;"><strong>{{ $message->authorLabel() }}</strong> wrote:</p>
        <p style="margin: 0; white-space: pre-wrap;">{{ strip_tags($message->body) }}</p>
    </div>

    <p style="margin: 24px 0;">
        <a href="{{ $trackUrl }}" style="display: inline-block; background-color: #119A48; color: #ffffff !important; padding: 12px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;">View full thread</a>
    </p>

    <p>Best regards,<br><strong>Africa CDC Knowledge Hub Team</strong></p>
</div>
@endsection
