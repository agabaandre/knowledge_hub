@extends('emails.layout')

@section('content')
<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2 style="color: #119A48; margin-bottom: 20px;">Publication Approved</h2>
    
    <p>Hello {{ $userName }},</p>
    
    <p>We are pleased to inform you that your @if($isSummary)summary/abstract @else publication @endif has been approved and is now live on the Knowledge Hub!</p>
    
    <div style="background: #f8f9fa;  padding: 15px; margin: 20px 0; border-radius: 0px;">
        <h3 style="margin-top: 0; color: #119A48;">{{ $publicationTitle }}</h3>
        
        @if($publicationDescription)
        <div style="margin-top: 15px;">
            <p style="margin: 0;"><strong>Description:</strong></p>
            <p style="margin: 5px 0 0 0;">{!! \Illuminate\Support\Str::words(strip_tags($publicationDescription), 50, '...') !!}</p>
        </div>
        @endif
    </div>
    
    <p>Your @if($isSummary)summary/abstract @else publication @endif is now accessible to all Knowledge Hub users and will be searchable in our database.</p>
    
    <div style="margin: 30px 0; text-align: center;">
        <a href="{{ $publicationUrl }}" 
           style="background: #119A48; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
            View Publication
        </a>
    </div>
    
    <p style="margin-top: 20px; color: #666; font-size: 0.9em;">
        Thank you for contributing to the Africa CDC Knowledge Hub. Your content helps advance public health knowledge and practice.
    </p>
    
    <p style="margin-top: 20px; color: #666; font-size: 0.9em;">
        If you have any questions or need assistance, please don't hesitate to contact our support team.
    </p>
    
    <p style="margin-top: 20px; color: #666; font-size: 0.9em;">
        Best regards,<br>
        Africa CDC Knowledge Hub
    </p>
</div>
@endsection

