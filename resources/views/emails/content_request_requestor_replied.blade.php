@extends('emails.layout')

@section('content')
<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <p>Hello,</p>
    <p>The requester added a message on a content request you are involved with:</p>

    <div style="background-color: #fff8e1; border-left: 4px solid #ffa000; padding: 15px; margin: 20px 0;">
        <p style="margin: 0; white-space: pre-wrap;">{{ strip_tags($message->body) }}</p>
    </div>

    <p style="margin: 24px 0;">
        <a href="{{ $discussUrl }}" style="display: inline-block; background-color: #119A48; color: #ffffff !important; padding: 12px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;">Open hub discussion</a>
    </p>

    <p>Best regards,<br><strong>Africa CDC Knowledge Hub Team</strong></p>
</div>
@endsection
