@extends('emails.layout')

@section('content')
<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <p>Hello {{ $name }},</p>
    <p>To help others find and collaborate with you on the Africa CDC Knowledge Hub, please complete your profile details.</p>

    <div style="background-color: #f8f9fa; padding: 15px; margin: 20px 0; border-radius: 4px;">
        <p style="margin: 0 0 8px 0;"><strong>Missing profile information:</strong></p>
        <ul style="margin: 0; padding-left: 18px;">
            @foreach($missingItems as $item)
                <li>{{ ucfirst($item) }}</li>
            @endforeach
        </ul>
    </div>

    <p style="margin: 24px 0;">
        <a href="{{ $accountUrl }}" style="display: inline-block; background-color: #119A48; color: #ffffff !important; padding: 12px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;">Update profile</a>
    </p>

    <p style="font-size: 13px; color: #666;">You can update your profile at any time from your account page.</p>
    <p>Best regards,<br><strong>Africa CDC Knowledge Hub Team</strong></p>
</div>
@endsection

