@extends('emails.layout')

@section('content')
<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2 style="color: #119A48; margin-bottom: 20px;">{{ __('emails.invite_title') }}</h2>
    
    <p>{{ __('emails.invite_hello') }}</p>
    
    <p>{!! __('emails.invite_body', [
        'inviter' => '<strong>'.e($inviterName).'</strong>',
        'community' => '<strong>'.e($community->community_name).'</strong>',
    ]) !!}</p>
    
    @if($community->description)
    <div style="background: #f8f9fa; padding: 15px; margin: 20px 0; border-radius: 0px;">
        <p style="margin: 0;"><strong>{{ __('emails.invite_about') }}</strong></p>
        <p style="margin: 10px 0 0 0;">{!! \Illuminate\Support\Str::words(strip_tags($community->description), 100, '...') !!}</p>
    </div>
    @endif
    
    <div style="margin: 30px 0; text-align: center;">
        <a href="{{ $acceptUrl }}" 
           style="background: #119A48; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
            {{ __('emails.invite_button') }}
        </a>
    </div>
    
    <p style="margin-top: 20px; color: #666; font-size: 0.9em;">
        {{ __('emails.invite_expires', [
            'date' => $invitation->expires_at->format('F d, Y'),
            'time' => $invitation->expires_at->format('g:i A'),
        ]) }}
    </p>
    
    <p style="margin-top: 20px; color: #666; font-size: 0.9em;">
        {{ __('emails.invite_questions') }}
    </p>
    
    <p style="margin-top: 20px; color: #666; font-size: 0.9em;">
        {{ __('emails.invite_regards') }}<br>
        {{ settings()->site_name ?? 'Africa CDC Knowledge Hub' }}
    </p>
    
    <p style="margin-top: 30px; color: #999; font-size: 0.85em; border-top: 1px solid #e2e8f0; padding-top: 20px;">
        {{ __('emails.invite_ignore') }}
    </p>
</div>
@endsection
