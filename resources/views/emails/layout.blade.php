<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $title ?? 'Africa CDC Knowledge Hub' }}</title>
    <!--[if mso]>
    <style type="text/css">
        body, table, td {font-family: Arial, Helvetica, sans-serif !important;}
    </style>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f6f9;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <!-- Main Container -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width: 600px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #119A48 0%, #0e7a3a 100%); padding: 30px 40px; border-radius: 8px 8px 0 0;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <a href="{{ url('/') }}" style="text-decoration: none; display: inline-block;">
                                            @php
                                                // Use Africa CDC official logo from their website
                                                $logoUrl = 'https://africacdc.org/wp-content/uploads/2020/02/AfricaCDC_Logo.png';
                                                // Fallback to settings logo if needed, but prefer the official one
                                                if (empty($logoUrl) || !filter_var($logoUrl, FILTER_VALIDATE_URL)) {
                                                    $logoUrl = settings()->logo ?? asset('assets/images/logo.png');
                                                    $logoUrl = filter_var($logoUrl, FILTER_VALIDATE_URL) ? $logoUrl : url($logoUrl);
                                                }
                                            @endphp
                                            <img src="{{ $logoUrl }}" alt="Africa CDC Knowledge Hub" style="max-width: 200px; height: auto; display: block;" />
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            @yield('content')
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 30px 40px; border-radius: 0 0 8px 8px; border-top: 1px solid #e2e8f0;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center" style="padding-bottom: 20px;">
                                        <p style="margin: 0; font-size: 14px; color: #64748b; line-height: 1.6;">
                                            <strong style="color: #119A48;">Africa CDC Knowledge Hub</strong><br>
                                            Comprehensive knowledge repository for public health resources
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-bottom: 20px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="padding: 0 10px;">
                                                    <a href="https://www.africacdc.org" style="color: #119A48; text-decoration: none; font-size: 14px;">Website</a>
                                                </td>
                                                <td style="padding: 0 10px; color: #cbd5e1;">|</td>
                                                <td style="padding: 0 10px;">
                                                    <a href="{{ url('/') }}" style="color: #119A48; text-decoration: none; font-size: 14px;">Knowledge Hub</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="border-top: 1px solid #e2e8f0; padding-top: 20px;">
                                        <p style="margin: 0; font-size: 12px; color: #94a3b8; line-height: 1.5;">
                                            This email was sent by Africa CDC Knowledge Hub<br>
                                            © {{ date('Y') }} Africa CDC. All rights reserved.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                
                <!-- Bottom Spacing -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width: 600px;">
                    <tr>
                        <td style="padding: 20px 0; text-align: center;">
                            <p style="margin: 0; font-size: 12px; color: #94a3b8;">
                                If you have any questions, please contact us at 
                                <a href="mailto:{{ config('emails.username', 'support@africacdc.org') }}" style="color: #119A48; text-decoration: none;">{{ config('emails.username', 'support@africacdc.org') }}</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

