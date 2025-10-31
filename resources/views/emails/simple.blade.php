@extends('emails.layout')

@section('content')
<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
        <td style="padding-bottom: 25px;">
            <h1 style="margin: 0; font-size: 26px; font-weight: 700; color: #1e293b; line-height: 1.3;">
                {{ $title ?? 'Notification' }}
            </h1>
        </td>
    </tr>
    
    <tr>
        <td style="padding-bottom: 25px;">
            <div style="font-size: 16px; color: #475569; line-height: 1.7;">
                {!! $content ?? '' !!}
            </div>
        </td>
    </tr>
    
    @if(isset($buttonText) && isset($buttonUrl))
    <tr>
        <td style="padding-top: 20px; padding-bottom: 20px;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td align="center">
                        <a href="{{ $buttonUrl }}" 
                           style="display: inline-block; padding: 14px 32px; background-color: #119A48; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 16px; line-height: 1.5;">
                            {{ $buttonText }}
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    @endif
</table>
@endsection
