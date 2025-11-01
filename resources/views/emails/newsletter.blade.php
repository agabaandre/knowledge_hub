@extends('emails.layout')

@section('content')
<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2 style="color: #119A48; margin-bottom: 20px;">{{ $subject }}</h2>
    
    <p>Hello {{ $subscriber->name ?: 'Subscriber' }},</p>
    
    <div style="background: #f8f9fa; border-left: 4px solid #119A48; padding: 15px; margin: 20px 0; border-radius: 4px;">
        {!! $content !!}
    </div>
    
    <p style="margin-top: 30px; color: #666; font-size: 0.9em;">
        Best regards,<br>
        Africa CDC Knowledge Hub
    </p>
    
    <p style="margin-top: 30px; color: #999; font-size: 0.85em; border-top: 1px solid #e2e8f0; padding-top: 20px;">
        You are receiving this email because you are subscribed to our mailing list.<br>
        <a href="{{ url('/unsubscribe?email=' . urlencode($subscriber->email)) }}" style="color: #119A48;">Unsubscribe</a>
    </p>
</div>
@endsection

