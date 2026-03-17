@extends('emails.layout')

@section('content')
<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <p>Dear Valued User,</p>
    
    <p>Thank you for your content request. We're pleased to inform you that your request has been processed and we have identified relevant content that addresses your needs.</p>
    
    <div style="background-color: #f8f9fa;  padding: 15px; margin: 20px 0; border-radius: 0px;">
        <h3 style="margin-top: 0; color: #119A48;">Your Request Details</h3>
        <p><strong>Subject:</strong> {{ $contentRequest->subject }}</p>
        <p><strong>Description:</strong> {{ $contentRequest->description }}</p>
        @if($contentRequest->country)
            <p><strong>Country:</strong> {{ $contentRequest->country->name }}</p>
        @endif
    </div>
    
    <div style="background-color: #e8f5e9; border-left: 4px solid #4caf50; padding: 15px; margin: 20px 0; border-radius: 4px;">
        <h3 style="margin-top: 0; color: #2e7d32;">Content Links</h3>
        <div style="white-space: pre-wrap; word-wrap: break-word;">{{ $contentLinks }}</div>
    </div>
    
    @if($adminComments)
    <div style="background-color: #fff3e0; border-left: 4px solid #ff9800; padding: 15px; margin: 20px 0; border-radius: 4px;">
        <h3 style="margin-top: 0; color: #e65100;">Additional Comments</h3>
        <p style="white-space: pre-wrap; word-wrap: break-word;">{{ $adminComments }}</p>
    </div>
    @endif
    
    <p>We hope these resources are helpful. If you need further assistance or have additional questions, please don't hesitate to reach out to us.</p>
    
    <p>Best regards,<br>
    <strong>Africa CDC Knowledge Hub Team</strong></p>
    
    <hr style="border: none; border-top: 1px solid #e0e0e0; margin: 30px 0;">
    
    <p style="font-size: 12px; color: #666;">
        This is an automated email regarding your content request submitted on {{ $contentRequest->created_at->format('F d, Y') }}.
        If you have any questions or concerns, please contact our support team.
    </p>
</div>
@endsection
