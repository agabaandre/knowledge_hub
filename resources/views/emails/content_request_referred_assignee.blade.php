@extends('emails.layout')

@section('content')
<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <p>Hello,</p>
    <p>You have been asked to help process or discuss a content request on the Knowledge Hub. Please sign in and open the discussion page to collaborate. The requester will see updates on their tracking page and receive emails when you post.</p>

    <div style="background-color: #f8f9fa; padding: 15px; margin: 20px 0; border-radius: 4px;">
        <p style="margin: 0 0 8px 0;"><strong>Subject:</strong> {{ $contentRequest->subject }}</p>
        @if($contentRequest->referral_notes)
            <p style="margin: 0; white-space: pre-wrap;"><strong>Notes from admin:</strong><br>{{ strip_tags($contentRequest->referral_notes) }}</p>
        @endif
    </div>

    <p style="margin: 24px 0;">
        <a href="{{ $discussUrl }}" style="display: inline-block; background-color: #119A48; color: #ffffff !important; padding: 12px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;">Open discussion (sign in required)</a>
    </p>

    <p style="font-size: 13px; color: #666;">Requester tracking link (for your reference only): <a href="{{ $trackUrl }}">{{ $trackUrl }}</a></p>

    <p>Thank you,<br><strong>Africa CDC Knowledge Hub Team</strong></p>
</div>
@endsection
