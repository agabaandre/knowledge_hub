@extends('emails.layout')

@section('content')
<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <p>Hello @if(!empty($recipientName)){{ $recipientName }}@else colleague@endif,</p>

    <div style="margin: 16px 0;">
        {!! $leadHtml !!}
    </div>

    @if(!empty($extraHtml))
        {!! $extraHtml !!}
    @endif

    <p style="margin: 24px 0;">
        <a href="{{ $threadUrl }}" style="display: inline-block; background-color: #119A48; color: #ffffff !important; padding: 12px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;">Open discussion</a>
    </p>

    <p style="font-size: 13px; color: #666;">You are receiving this because you started this discussion, commented, liked a post, or follow this thread on the Africa Health Knowledge Hub.</p>

    <p>Best regards,<br><strong>Africa CDC Knowledge Hub Team</strong></p>
</div>
@endsection
