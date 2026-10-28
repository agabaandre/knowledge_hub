@extends('emails.layout')

@section('content')
<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2 style="color: #119A48; margin-bottom: 20px;">{{ __('emails.publication_approved_title') }}</h2>
    
    <p>{{ __('emails.publication_approved_hello', ['name' => $userName]) }}</p>
    
    <p>{{ !empty($isSummary) ? __('emails.publication_approved_body_summary') : __('emails.publication_approved_body_publication') }}</p>
    
    <div style="background: #f8f9fa;  padding: 15px; margin: 20px 0; border-radius: 0px;">
        <h3 style="margin-top: 0; color: #119A48;">{{ $publicationTitle }}</h3>
        
        @if($publicationDescription)
        <div style="margin-top: 15px;">
            <p style="margin: 0;"><strong>{{ __('emails.publication_description') }}</strong></p>
            <p style="margin: 5px 0 0 0;">{!! \Illuminate\Support\Str::words(strip_tags($publicationDescription), 50, '...') !!}</p>
        </div>
        @endif
    </div>
    
    <p>{{ !empty($isSummary) ? __('emails.publication_approved_live_summary') : __('emails.publication_approved_live_publication') }}</p>
    
    <div style="margin: 30px 0; text-align: center;">
        <a href="{{ $publicationUrl }}" 
           style="background: #119A48; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
            {{ __('emails.publication_view') }}
        </a>
    </div>
    
    <p style="margin-top: 20px; color: #666; font-size: 0.9em;">
        {{ __('emails.publication_thanks') }}
    </p>
    
    <p style="margin-top: 20px; color: #666; font-size: 0.9em;">
        {{ __('emails.publication_support') }}
    </p>
    
    <p style="margin-top: 20px; color: #666; font-size: 0.9em;">
        {{ __('emails.publication_regards') }}<br>
        {{ settings()->site_name ?? 'Africa CDC Knowledge Hub' }}
    </p>
</div>
@endsection
