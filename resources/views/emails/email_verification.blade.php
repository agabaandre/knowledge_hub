@extends('emails.layout')

@section('content')
<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
        <td style="padding-bottom: 30px;">
            <h1 style="margin: 0; font-size: 28px; font-weight: 700; color: #1e293b; line-height: 1.3;">
                Verify Your Email Address
            </h1>
        </td>
    </tr>
    
    <tr>
        <td style="padding-bottom: 25px;">
            <p style="margin: 0; font-size: 16px; color: #475569; line-height: 1.6;">
                Thank you for registering with Africa CDC Knowledge Hub! To complete your registration and start accessing our resources, please verify your email address by clicking the button below.
            </p>
        </td>
    </tr>
    
    <tr>
        <td style="padding-bottom: 30px;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td align="center">
                        <a href="{{ route('account_verify') }}?t={{ $token }}" 
                           style="display: inline-block; padding: 14px 32px; background-color: #119A48; color: #ffffff; text-decoration: none; border-radius: 0px; font-weight: 600; font-size: 16px; line-height: 1.5;">
                            Verify Email Address
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr>
        <td style="padding-bottom: 20px;">
            <p style="margin: 0; font-size: 14px; color: #64748b; line-height: 1.6;">
                If the button doesn't work, you can copy and paste this link into your browser:
            </p>
        </td>
    </tr>
    
    <tr>
        <td style="padding-bottom: 30px;">
            <p style="margin: 0; padding: 12px; background-color: #f1f5f9; border-radius: 4px; border-left: 3px solid #119A48;">
                <a href="{{ route('account_verify') }}?t={{ $token }}" 
                   style="color: #119A48; text-decoration: none; word-break: break-all; font-size: 13px; line-height: 1.5;">
                    {{ route('account_verify') }}?t={{ $token }}
                </a>
            </p>
        </td>
    </tr>
    
    <tr>
        <td style="padding-top: 20px; border-top: 1px solid #e2e8f0;">
            <p style="margin: 0; font-size: 13px; color: #94a3b8; line-height: 1.6;">
                <strong>Important:</strong> This verification link will expire in 24 hours. If you didn't create an account with us, please ignore this email.
            </p>
        </td>
    </tr>
</table>
@endsection
