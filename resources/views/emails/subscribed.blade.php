@extends('emails.layout')

@section('content')
<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
        <td align="center" style="padding-bottom: 25px;">
            <div style="width: 64px; height: 64px; background-color: #dcfce7; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">
                <span style="font-size: 32px; color: #119A48;">✓</span>
            </div>
        </td>
    </tr>
    
    <tr>
        <td style="padding-bottom: 25px;">
            <h1 style="margin: 0; font-size: 28px; font-weight: 700; color: #1e293b; line-height: 1.3; text-align: center;">
                Subscription Successful!
            </h1>
        </td>
    </tr>
    
    <tr>
        <td style="padding-bottom: 25px;">
            <p style="margin: 0; font-size: 16px; color: #475569; line-height: 1.7; text-align: center;">
                Thank you for subscribing to Africa CDC Knowledge Hub! You're now part of our community and will receive regular updates about new publications, resources, and health information.
            </p>
        </td>
    </tr>
    
    <tr>
        <td style="padding-bottom: 30px;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td align="center">
                        <a href="{{ url('/') }}" 
                           style="display: inline-block; padding: 14px 32px; background-color: #119A48; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 16px; line-height: 1.5;">
                            Explore Knowledge Hub
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr>
        <td style="padding: 20px; background-color: #f0fdf4; border-radius: 6px; border-left: 4px solid #119A48;">
            <p style="margin: 0; font-size: 14px; color: #166534; line-height: 1.6;">
                <strong>What's next?</strong><br>
                You'll receive notifications about new publications, health alerts, and important updates from Africa CDC. You can manage your subscription preferences anytime from your account settings.
            </p>
        </td>
    </tr>
</table>
@endsection
