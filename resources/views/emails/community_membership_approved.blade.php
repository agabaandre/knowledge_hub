@extends('emails.layout')

@section('content')
<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2 style="color: #119A48; margin-bottom: 20px;">Community Membership Approved</h2>
    
    <p>Hello {{ $memberName }},</p>
    
    <p>We are pleased to inform you that your membership request for the <strong>{{ $communityName }}</strong> community has been approved!</p>
    
    @if($communityDescription)
    <div style="background: #f8f9fa; border-left: 4px solid #119A48; padding: 15px; margin: 20px 0; border-radius: 4px;">
        <p style="margin: 0;"><strong>About this community:</strong></p>
        <p style="margin: 10px 0 0 0;">{!! \Illuminate\Support\Str::words(strip_tags($communityDescription), 100, '...') !!}</p>
    </div>
    @endif
    
    <p>As a member of this community, you can now:</p>
    <ul style="margin: 15px 0; padding-left: 20px;">
        <li>Access community publications and resources</li>
        <li>Participate in forum discussions</li>
        <li>Connect with other community members</li>
        <li>Share your expertise and knowledge</li>
    </ul>
    
    <div style="margin: 30px 0; text-align: center;">
        <a href="{{ $communityUrl }}" 
           style="background: #119A48; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
            Visit Community
        </a>
    </div>
    
    <p style="margin-top: 20px; color: #666; font-size: 0.9em;">
        If you have any questions or need assistance, please don't hesitate to contact the community administrators.
    </p>
    
    <p style="margin-top: 20px; color: #666; font-size: 0.9em;">
        Best regards,<br>
        Africa CDC Knowledge Hub
    </p>
</div>
@endsection

