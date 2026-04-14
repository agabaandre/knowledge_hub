@extends('emails.layout')

@section('content')
<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <p>Dear colleague,</p>
    <p>Here is an <strong>AI-generated overview</strong> of the forum discussion opened for your content request <strong>{{ $contentRequest->subject }}</strong>. It reflects the thread at the time this email was sent; open the forum for the latest comments.</p>

    <p style="margin: 20px 0;">
        <a href="{{ $forumUrl }}" style="display: inline-block; background-color: #119A48; color: #ffffff !important; padding: 12px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;">Open forum thread</a>
    </p>

    <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin: 20px 0; background: #fafafa;">
        {!! $summaryHtml !!}
    </div>

    <p style="font-size: 12px; color: #666;">AI summaries are for guidance only and may omit details. Always review the full discussion on the hub.</p>

    <p>Best regards,<br><strong>Africa CDC Knowledge Hub Team</strong></p>
</div>
@endsection
