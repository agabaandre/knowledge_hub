@extends('emails.layout')

@section('content')
<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
        <td align="center" style="padding-bottom: 25px;">
            <div style="width: 64px; height: 64px; background-color: #fee2e2; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">
                <span style="font-size: 32px; color: #dc2626;">✕</span>
            </div>
        </td>
    </tr>
    
    <tr>
        <td style="padding-bottom: 25px;">
            <h1 style="margin: 0; font-size: 28px; font-weight: 700; color: #1e293b; line-height: 1.3; text-align: center;">
                Successfully Unsubscribed
            </h1>
        </td>
    </tr>
    
    <tr>
        <td style="padding-bottom: 25px;">
            <p style="margin: 0; font-size: 16px; color: #475569; line-height: 1.7; text-align: center;">
                We're sorry to see you go. You have successfully unsubscribed from our email notifications. You will no longer receive updates from Africa CDC Knowledge Hub.
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
                            Visit Knowledge Hub
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr>
        <td style="padding: 20px; background-color: #fef2f2; border-radius: 6px; border-left: 4px solid #dc2626;">
            <p style="margin: 0; font-size: 14px; color: #991b1b; line-height: 1.6;">
                <strong>We value your feedback</strong><br>
                If you have any suggestions or feedback about why you unsubscribed, please don't hesitate to contact us. We're always looking to improve our services.
            </p>
        </td>
    </tr>
    
    <tr>
        <td style="padding-top: 30px; text-align: center;">
            <p style="margin: 0; font-size: 14px; color: #64748b; line-height: 1.6;">
                Changed your mind? You can always resubscribe from your account settings or by visiting our website.
            </p>
        </td>
    </tr>
</table>
@endsection
