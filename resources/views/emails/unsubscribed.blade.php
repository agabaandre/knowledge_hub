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
                {{ __('emails.unsubscribed_title') }}
            </h1>
        </td>
    </tr>
    
    <tr>
        <td style="padding-bottom: 25px;">
            <p style="margin: 0; font-size: 16px; color: #475569; line-height: 1.7; text-align: center;">
                {{ __('emails.unsubscribed_body') }}
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
                            {{ __('emails.layout_knowledge_hub') }}
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr>
        <td style="padding-top: 10px; text-align: center;">
            <p style="margin: 0; font-size: 14px; color: #64748b; line-height: 1.6;">
                {{ __('emails.unsubscribed_resub') }}
            </p>
        </td>
    </tr>
</table>
@endsection
