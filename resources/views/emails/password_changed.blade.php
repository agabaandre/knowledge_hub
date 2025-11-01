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
                Password Reset Successful
            </h1>
        </td>
    </tr>
    
    <tr>
        <td style="padding-bottom: 25px;">
            <p style="margin: 0; font-size: 16px; color: #475569; line-height: 1.6;">
                Hello {{ $name ?? 'User' }},
            </p>
        </td>
    </tr>
    
    <tr>
        <td style="padding-bottom: 25px;">
            <p style="margin: 0; font-size: 16px; color: #475569; line-height: 1.6;">
                Your password has been successfully reset. Your temporary password is:
            </p>
        </td>
    </tr>
    
    <tr>
        <td style="padding-bottom: 30px;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td align="center" style="padding: 20px; background-color: #f0fdf4; border-radius: 6px; border: 2px solid #119A48;">
                        <p style="margin: 0; font-size: 24px; font-weight: 700; color: #119A48; letter-spacing: 2px; font-family: 'Courier New', monospace;">
                            {{ $password ?? 'N/A' }}
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr>
        <td style="padding-bottom: 30px;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td align="center">
                        <a href="{{ url('login') }}" 
                           style="display: inline-block; padding: 14px 32px; background-color: #119A48; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 16px; line-height: 1.5;">
                            Login Now
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr>
        <td style="padding: 20px; background-color: #fef2f2; border-radius: 6px; border-left: 4px solid #dc2626;">
            <p style="margin: 0; font-size: 14px; color: #991b1b; line-height: 1.6;">
                <strong>⚠️ Important Security Notice:</strong><br>
                Please login immediately and change your password to something secure that only you know. For your security, never share your password with anyone.
            </p>
        </td>
    </tr>
</table>
@endsection

