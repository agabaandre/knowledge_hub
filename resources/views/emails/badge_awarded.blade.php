@extends('emails.layout')

@section('content')
<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2 style="color: #119A48; margin-bottom: 20px;">🎖️ Congratulations! You Earned a Badge!</h2>
    
    <p>Hello {{ $userName }},</p>
    
    <p>We are thrilled to inform you that you have earned the <strong style="color: {{ $badgeType->badge_color }};">{{ $badgeType->name }}</strong> badge for your outstanding contributions to the <strong>{{ $communityName }}</strong> community!</p>
    
    <div style="background: linear-gradient(135deg, {{ $badgeType->badge_color }}15 0%, {{ $badgeType->badge_color }}05 100%); border-left: 4px solid {{ $badgeType->badge_color }}; padding: 20px; margin: 25px 0; border-radius: 8px; text-align: center;">
        <div style="font-size: 48px; margin-bottom: 10px;">
            @if($badgeType->slug === 'silver')
                🥈
            @elseif($badgeType->slug === 'gold')
                🥇
            @elseif($badgeType->slug === 'platinum')
                💎
            @elseif($badgeType->slug === 'diamond')
                💠
            @else
                🏅
            @endif
        </div>
        <h3 style="margin: 10px 0; color: {{ $badgeType->badge_color }}; font-size: 1.5em;">{{ $badgeType->name }}</h3>
        <p style="margin: 5px 0; font-weight: 600;">{{ $badgeType->description }}</p>
        <p style="margin: 10px 0 0 0; font-size: 0.95em; color: #666;">
            <strong>Your Contributions:</strong> {{ $contributions }} contributions in {{ $monthYear }}
        </p>
    </div>
    
    <div style="background: #f8f9fa; padding: 15px; margin: 20px 0; border-radius: 4px;">
        <p style="margin: 0;"><strong>Badge Details:</strong></p>
        <ul style="margin: 10px 0 0 20px; padding: 0;">
            <li>Community: <strong>{{ $communityName }}</strong></li>
            <li>Period: <strong>{{ $monthYear }}</strong></li>
            <li>Contributions: <strong>{{ $contributions }}</strong> (publications, forum posts, and comments)</li>
            <li>Badge Level: <strong>{{ $badgeType->name }}</strong></li>
        </ul>
    </div>
    
    <p>Your badge is now displayed on your profile within the community, showcasing your commitment to knowledge sharing and collaboration.</p>
    
    <div style="margin: 30px 0; text-align: center;">
        <a href="{{ $communityUrl }}" 
           style="background: #119A48; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
            Visit Community to See Your Badge
        </a>
    </div>
    
    <p style="margin-top: 20px; color: #666; font-size: 0.9em;">
        <strong>Keep Contributing!</strong> Continue sharing publications and engaging in discussions to earn higher-level badges:
    </p>
    <ul style="margin: 10px 0 20px 20px; color: #666; font-size: 0.9em;">
        <li>🥈 <strong>Silver Contributor:</strong> 5+ contributions/month</li>
        <li>🥇 <strong>Gold Contributor:</strong> 10+ contributions/month</li>
        <li>💎 <strong>Platinum Contributor:</strong> 20+ contributions/month</li>
        <li>💠 <strong>Diamond Contributor (Hall of Honor):</strong> 40+ contributions/month</li>
    </ul>
    
    <p style="margin-top: 20px; color: #666; font-size: 0.9em;">
        Thank you for being an active member of the Africa CDC Knowledge Hub community!
    </p>
    
    <p style="margin-top: 20px; color: #666; font-size: 0.9em;">
        Best regards,<br>
        Africa CDC Knowledge Hub Team
    </p>
</div>
@endsection

