@extends('emails.layout')

@section('content')
<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <p>Dear colleague,</p>
    <p>Your content request has been referred to a colleague or community on the Africa Health Knowledge Hub for follow-up and discussion. You can read all updates and add your own notes using your private link below (no login required).</p>

    <div style="background-color: #f8f9fa; padding: 15px; margin: 20px 0; border-radius: 4px;">
        <p style="margin: 0 0 8px 0;"><strong>Subject:</strong> {{ $contentRequest->subject }}</p>
        @if($contentRequest->country)
            <p style="margin: 0;"><strong>Country:</strong> {{ $contentRequest->country->name }}</p>
        @endif
    </div>

    <p style="margin: 24px 0;">
        <a href="{{ $trackUrl }}" style="display: inline-block; background-color: #119A48; color: #ffffff !important; padding: 12px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;">View updates &amp; private notes</a>
    </p>

    @if(!empty($contentRequest->referral_forum_id))
    <p>Your request is also being discussed in a <strong>dedicated community forum thread</strong> on the Knowledge Hub (members comment there; you may need to sign in to read everything).</p>
    <p style="margin: 16px 0;">
        <a href="{{ url('forums/thread?id='.$contentRequest->referral_forum_id) }}" style="display: inline-block; background-color: #0d7a3a; color: #ffffff !important; padding: 12px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;">Open forum thread</a>
    </p>
    <p style="font-size: 13px; color: #666;">You may receive a separate email with an AI-generated overview of the discussion.</p>
    @endif

    <p style="font-size: 13px; color: #666;">Keep your tracking link private. You will receive email notifications when there are new messages from the team and when there are new forum comments.</p>

    <p>Best regards,<br><strong>Africa CDC Knowledge Hub Team</strong></p>
</div>
@endsection
