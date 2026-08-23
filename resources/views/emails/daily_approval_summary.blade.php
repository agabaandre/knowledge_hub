@extends('emails.layout')

@section('content')
<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2 style="color: #119A48; margin-bottom: 20px;">Daily Approval Summary</h2>
    
    <p>Hello {{ $approverName }},</p>
    
    <p>Here is a summary of all pending items awaiting your approval:</p>
    
    <div style="background: #f8f9fa;  padding: 20px; margin: 20px 0; border-radius: 0px;">
        <h3 style="margin-top: 0; color: #119A48;">Pending Items Overview</h3>
        <table style="width: 100%; border-collapse: collapse;">
            @if($totalForums > 0)
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;"><strong>Forums:</strong></td>
                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; text-align: right;"><strong>{{ $totalForums }}</strong></td>
            </tr>
            @endif
            @if($totalPublications > 0)
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;"><strong>Publications:</strong></td>
                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; text-align: right;"><strong>{{ $totalPublications }}</strong></td>
            </tr>
            @endif
            @if($totalForumComments > 0)
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;"><strong>Forum Comments:</strong></td>
                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; text-align: right;"><strong>{{ $totalForumComments }}</strong></td>
            </tr>
            @endif
            @if($totalPublicationComments > 0)
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;"><strong>Publication Comments:</strong></td>
                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; text-align: right;"><strong>{{ $totalPublicationComments }}</strong></td>
            </tr>
            @endif
            @if($totalCopApprovals > 0)
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;"><strong>COP Member Approvals:</strong></td>
                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; text-align: right;"><strong>{{ $totalCopApprovals }}</strong></td>
            </tr>
            @endif
            @if(($totalFederated ?? 0) > 0)
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;"><strong>Federated Content:</strong></td>
                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; text-align: right;"><strong>{{ $totalFederated }}</strong></td>
            </tr>
            @endif
            <tr>
                <td style="padding: 12px 0 8px 0; font-size: 1.1em;"><strong>Total Pending:</strong></td>
                <td style="padding: 12px 0 8px 0; text-align: right; font-size: 1.1em;"><strong style="color: #119A48;">{{ $totalPending }}</strong></td>
            </tr>
        </table>
    </div>

    @if($pendingForums->count() > 0)
    <div style="margin: 30px 0;">
        <h3 style="color: #119A48; margin-bottom: 15px;">Pending Forums ({{ $totalForums }})</h3>
        @foreach($pendingForums as $forum)
        <div style="background: #fff; border: 1px solid #e2e8f0; padding: 15px; margin-bottom: 10px; border-radius: 4px;">
            <h4 style="margin: 0 0 8px 0; color: #2d3748;">{{ $forum->forum_title ?? 'Untitled Forum' }}</h4>
            <p style="margin: 5px 0; color: #666; font-size: 0.9em;"><strong>Author:</strong> {{ $forum->user->name ?? 'Unknown' }}</p>
            @if($forum->forum_description)
            <p style="margin: 8px 0 0 0; color: #666;">{!! \Illuminate\Support\Str::words(strip_tags($forum->forum_description), 30, '...') !!}</p>
            @endif
            <p style="margin: 10px 0 0 0;">
                <a href="{{ url('/admin/approvals') }}?type=forum&q={{ urlencode($forum->forum_title ?? '') }}" 
                   style="background: #119A48; color: #ffffff; padding: 8px 20px; text-decoration: none; border-radius: 4px; display: inline-block; font-size: 0.9em;">
                    Review Forum
                </a>
            </p>
        </div>
        @endforeach
        @if($totalForums > $pendingForums->count())
        <p style="color: #666; font-size: 0.9em; margin-top: 10px;">
            <a href="{{ url('/admin/approvals') }}?type=forum" style="color: #119A48;">View all {{ $totalForums }} pending forums →</a>
        </p>
        @endif
    </div>
    @endif

    @if($pendingPublications->count() > 0)
    <div style="margin: 30px 0;">
        <h3 style="color: #119A48; margin-bottom: 15px;">Pending Publications ({{ $totalPublications }})</h3>
        @foreach($pendingPublications as $publication)
        <div style="background: #fff; border: 1px solid #e2e8f0; padding: 15px; margin-bottom: 10px; border-radius: 4px;">
            <h4 style="margin: 0 0 8px 0; color: #2d3748;">{{ $publication->title ?? 'Untitled Publication' }}</h4>
            <p style="margin: 5px 0; color: #666; font-size: 0.9em;"><strong>Author:</strong> {{ $publication->author->name ?? ($publication->user->name ?? 'Unknown') }}</p>
            @if($publication->description)
            <p style="margin: 8px 0 0 0; color: #666;">{!! \Illuminate\Support\Str::words(strip_tags($publication->description), 30, '...') !!}</p>
            @endif
            <p style="margin: 10px 0 0 0;">
                <a href="{{ url('/admin/approvals') }}?type=publication&q={{ urlencode($publication->title ?? '') }}" 
                   style="background: #119A48; color: #ffffff; padding: 8px 20px; text-decoration: none; border-radius: 4px; display: inline-block; font-size: 0.9em;">
                    Review Publication
                </a>
            </p>
        </div>
        @endforeach
        @if($totalPublications > $pendingPublications->count())
        <p style="color: #666; font-size: 0.9em; margin-top: 10px;">
            <a href="{{ url('/admin/approvals') }}?type=publication" style="color: #119A48;">View all {{ $totalPublications }} pending publications →</a>
        </p>
        @endif
    </div>
    @endif

    @if($pendingForumComments->count() > 0)
    <div style="margin: 30px 0;">
        <h3 style="color: #119A48; margin-bottom: 15px;">Pending Forum Comments ({{ $totalForumComments }})</h3>
        @foreach($pendingForumComments as $comment)
        <div style="background: #fff; border: 1px solid #e2e8f0; padding: 15px; margin-bottom: 10px; border-radius: 4px;">
            <p style="margin: 5px 0; color: #666; font-size: 0.9em;"><strong>By:</strong> {{ $comment->user->name ?? 'Anonymous' }}</p>
            <p style="margin: 8px 0 0 0; color: #666;">{!! \Illuminate\Support\Str::words(strip_tags($comment->comment ?? ''), 30, '...') !!}</p>
            <p style="margin: 10px 0 0 0;">
                <a href="{{ url('/admin/approvals') }}?type=forum" 
                   style="background: #119A48; color: #ffffff; padding: 8px 20px; text-decoration: none; border-radius: 4px; display: inline-block; font-size: 0.9em;">
                    Review Comments
                </a>
            </p>
        </div>
        @endforeach
    </div>
    @endif

    @if($pendingPublicationComments->count() > 0)
    <div style="margin: 30px 0;">
        <h3 style="color: #119A48; margin-bottom: 15px;">Pending Publication Comments ({{ $totalPublicationComments }})</h3>
        @foreach($pendingPublicationComments as $comment)
        <div style="background: #fff; border: 1px solid #e2e8f0; padding: 15px; margin-bottom: 10px; border-radius: 4px;">
            <p style="margin: 5px 0; color: #666; font-size: 0.9em;"><strong>By:</strong> {{ $comment->user->name ?? 'Anonymous' }}</p>
            <p style="margin: 8px 0 0 0; color: #666;">{!! \Illuminate\Support\Str::words(strip_tags($comment->comment ?? ''), 30, '...') !!}</p>
            <p style="margin: 10px 0 0 0;">
                <a href="{{ url('/admin/approvals') }}?type=publication" 
                   style="background: #119A48; color: #ffffff; padding: 8px 20px; text-decoration: none; border-radius: 4px; display: inline-block; font-size: 0.9em;">
                    Review Comments
                </a>
            </p>
        </div>
        @endforeach
    </div>
    @endif

    @if($pendingCopApprovals->count() > 0)
    <div style="margin: 30px 0;">
        <h3 style="color: #119A48; margin-bottom: 15px;">Pending COP Member Approvals ({{ $totalCopApprovals }})</h3>
        @foreach($pendingCopApprovals as $approval)
        <div style="background: #fff; border: 1px solid #e2e8f0; padding: 15px; margin-bottom: 10px; border-radius: 4px;">
            <h4 style="margin: 0 0 8px 0; color: #2d3748;">{{ $approval->community->community_name ?? 'Unknown Community' }}</h4>
            <p style="margin: 5px 0; color: #666; font-size: 0.9em;"><strong>User:</strong> {{ $approval->user->name ?? 'Unknown' }}</p>
            <p style="margin: 5px 0; color: #666; font-size: 0.9em;"><strong>Email:</strong> {{ $approval->user->email ?? 'N/A' }}</p>
            <p style="margin: 10px 0 0 0;">
                <a href="{{ url('/admin/approvals') }}?type=cop_participant" 
                   style="background: #119A48; color: #ffffff; padding: 8px 20px; text-decoration: none; border-radius: 4px; display: inline-block; font-size: 0.9em;">
                    Review Community
                </a>
            </p>
        </div>
        @endforeach
        @if($totalCopApprovals > $pendingCopApprovals->count())
        <p style="color: #666; font-size: 0.9em; margin-top: 10px;">
            <a href="{{ url('/admin/approvals') }}?type=cop_participant" style="color: #119A48;">View all {{ $totalCopApprovals }} pending approvals →</a>
        </p>
        @endif
    </div>
    @endif

    @if(($pendingFederated ?? collect())->count() > 0)
    <div style="margin: 30px 0;">
        <h3 style="color: #119A48; margin-bottom: 15px;">Pending Federated Content ({{ $totalFederated }})</h3>
        @foreach($pendingFederated as $item)
        <div style="background: #fff; border: 1px solid #e2e8f0; padding: 15px; margin-bottom: 10px; border-radius: 4px;">
            <h4 style="margin: 0 0 8px 0; color: #2d3748;">{{ $item->title ?? 'Untitled federated item' }}</h4>
            <p style="margin: 5px 0; color: #666; font-size: 0.9em;"><strong>Hub:</strong> {{ $item->hub->name ?? 'Partner hub' }}</p>
            <p style="margin: 10px 0 0 0;">
                <a href="{{ url('/admin/approvals') }}?type=federated"
                   style="background: #119A48; color: #ffffff; padding: 8px 20px; text-decoration: none; border-radius: 4px; display: inline-block; font-size: 0.9em;">
                    Review Federated Content
                </a>
            </p>
        </div>
        @endforeach
        @if($totalFederated > $pendingFederated->count())
        <p style="color: #666; font-size: 0.9em; margin-top: 10px;">
            <a href="{{ url('/admin/approvals') }}?type=federated" style="color: #119A48;">View all {{ $totalFederated }} pending federated items →</a>
        </p>
        @endif
    </div>
    @endif

    <div style="margin: 30px 0; padding: 20px; background: #f8f9fa; border-radius: 4px; text-align: center;">
        <a href="{{ url('/admin/approvals') }}"
           style="background: #119A48; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
            Review & Approve in Approvals
        </a>
    </div>
    
    <p style="margin-top: 30px; color: #666; font-size: 0.9em;">
        You are receiving this email because you have permission to moderate content on the platform.
    </p>
    
    <p style="margin-top: 20px; color: #666; font-size: 0.9em;">
        Best regards,<br>
        Africa CDC Knowledge Hub
    </p>
</div>
@endsection

