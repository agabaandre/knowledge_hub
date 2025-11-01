@extends('emails.layout')

@section('content')
<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2 style="color: #119A48; margin-bottom: 20px;">New {{ ucfirst($contentType) }} Posted</h2>
    
    <p>Hello {{ $memberName }},</p>
    
    <p>A new {{ $contentType }} has been posted to <strong>{{ $communityName }}</strong> community:</p>
    
    <div style="background: #f8f9fa; border-left: 4px solid #119A48; padding: 15px; margin: 20px 0; border-radius: 4px;">
        <h3 style="margin-top: 0; color: #119A48;">{{ $contentTitle }}</h3>
        
        @if($authorName)
        <p style="margin: 10px 0;"><strong>Author:</strong> {{ $authorName }}</p>
        @endif
        
        @if($contentDescription)
        <div style="margin-top: 15px;">
            <p style="margin: 0;"><strong>Description:</strong></p>
            <p style="margin: 5px 0 0 0;">{!! \Illuminate\Support\Str::words(strip_tags($contentDescription), 50, '...') !!}</p>
        </div>
        @endif
    </div>
    
    <div style="margin: 30px 0; text-align: center;">
        <a href="{{ $contentUrl }}" 
           style="background: #119A48; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
            View {{ ucfirst($contentType) }}
        </a>
    </div>
    
    <p style="margin-top: 30px; color: #666; font-size: 0.9em;">
        You are receiving this email because you are a member of the <strong>{{ $communityName }}</strong> community.
    </p>
    
    <p style="margin-top: 20px; color: #666; font-size: 0.9em;">
        Best regards,<br>
        Africa CDC Knowledge Hub
    </p>
</div>
@endsection

