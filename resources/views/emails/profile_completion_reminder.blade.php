@extends('emails.layout')

@section('content')
<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2 style="color: #119A48; margin-bottom: 20px;">{{ __('emails.profile_reminder_title') }}</h2>
    <p>{{ __('emails.reset_hello', ['name' => $name]) }}</p>
    <p>{{ __('emails.profile_reminder_body') }}</p>

    <div style="background-color: #f8f9fa; padding: 15px; margin: 20px 0; border-radius: 4px;">
        <p style="margin: 0 0 8px 0;"><strong>{{ __('emails.profile_reminder_title') }}:</strong></p>
        <ul style="margin: 0; padding-left: 18px;">
            @foreach($missingItems as $item)
                <li>{{ ucfirst($item) }}</li>
            @endforeach
        </ul>
    </div>

    <p style="margin: 24px 0;">
        <a href="{{ $accountUrl }}" style="display: inline-block; background-color: #119A48; color: #ffffff !important; padding: 12px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;">{{ __('emails.profile_reminder_button') }}</a>
    </p>

    <p style="font-size: 13px; color: #666;">{{ __('emails.publication_support') }}</p>
    <p>{{ __('emails.publication_regards') }}<br><strong>{{ settings()->site_name ?? 'Africa CDC Knowledge Hub' }}</strong></p>
</div>
@endsection
