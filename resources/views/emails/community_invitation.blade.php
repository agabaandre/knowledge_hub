@extends('emails.layout')

@section('content')
<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2 style="color: #119A48; margin-bottom: 20px;">Community Invitation</h2>
    
    <p>Hello,</p>
    
    <p><strong>{{ $inviterName }}</strong> has invited you to join the <strong>{{ $community->community_name }}</strong> community.</p>
    
    @if($community->description)
    <div style="background: #f8f9fa; border-left: 4px solid #119A48; padding: 15px; margin: 20px 0; border-radius: 4px;">
        <p style="margin: 0;"><strong>About this community:</strong></p>
        <p style="margin: 10px 0 0 0;">{!! \Illuminate\Support\Str::words(strip_tags($community->description), 100, '...') !!}</p>
    </div>
    @endif
    
    <div style="margin: 30px 0; text-align: center;">
        <a href="{{ $acceptUrl }}" 
           style="background: #119A48; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
            Accept Invitation
        </a>
    </div>
    
    <p style="margin-top: 20px; color: #666; font-size: 0.9em;">
        <strong>Important:</strong> This invitation will expire on {{ $invitation->expires_at->format('F d, Y') }} at {{ $invitation->expires_at->format('g:i A') }}.
    </p>
    
    <p style="margin-top: 20px; color: #666; font-size: 0.9em;">
        If you have any questions, please contact the community administrator.
    </p>
    
    <p style="margin-top: 20px; color: #666; font-size: 0.9em;">
        Best regards,<br>
        Africa CDC Knowledge Hub
    </p>
    
    <p style="margin-top: 30px; color: #999; font-size: 0.85em; border-top: 1px solid #e2e8f0; padding-top: 20px;">
        If you did not expect this invitation, you can safely ignore this email.
    </p>
</div>
@endsection

