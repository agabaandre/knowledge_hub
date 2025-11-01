<?php

namespace App\Mail;

use App\Models\CommunityInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CommunityInvitationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $invitation;
    public $community;
    public $inviterName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(CommunityInvitation $invitation)
    {
        $this->invitation = $invitation;
        $this->community = $invitation->community;
        $this->inviterName = $invitation->inviter->name ?? 'Administrator';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $acceptUrl = url('/communities/accept-invitation/' . $this->invitation->token);
        
        return $this->subject('Invitation to Join: ' . $this->community->community_name)
                    ->view('emails.community_invitation')
                    ->with([
                        'invitation' => $this->invitation,
                        'community' => $this->community,
                        'inviterName' => $this->inviterName,
                        'acceptUrl' => $acceptUrl,
                    ]);
    }
}
