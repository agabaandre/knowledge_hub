@extends('emails.layout')

@section('content')
<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <p>Dear colleague,</p>
    <p>Someone commented on the <strong>forum discussion</strong> linked to your content request.</p>

    <div style="background-color: #f0fdf4; border-left: 4px solid #119A48; padding: 15px; margin: 20px 0;">
        <p style="margin: 0 0 8px 0;"><strong>{{ $comment->user->name ?? 'A community member' }}</strong> wrote:</p>
        <p style="margin: 0; white-space: pre-wrap;">{{ Str::limit(strip_tags($comment->comment ?? ''), 600) }}</p>
    </div>

    <p style="margin: 24px 0;">
        <a href="{{ $forumUrl }}" style="display: inline-block; background-color: #119A48; color: #ffffff !important; padding: 12px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;">Open forum thread</a>
    </p>

    <p style="font-size: 13px; color: #666;">Your private tracking link still works for notes and updates outside the forum.</p>

    <p>Best regards,<br><strong>Africa CDC Knowledge Hub Team</strong></p>
</div>
@endsection
