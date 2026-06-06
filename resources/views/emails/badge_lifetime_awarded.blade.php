<p>Hello {{ $userName }},</p>

<p>Congratulations! You have earned the <strong>{{ $badgeType->name }}</strong> contributor badge on the Africa Health Knowledge Hub.</p>

<p>You have <strong>{{ number_format($lifetimeContributions) }}</strong> approved lifetime contributions across published resources, forum threads, and comments on the hub.</p>

<p><a href="{{ $profileUrl }}">View your public contributor profile</a></p>

<p>Thank you for sharing knowledge that strengthens public health across Africa.</p>
