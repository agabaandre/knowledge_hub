<p>Hello {{ $userName }},</p>

<p>Congratulations! Your <strong>{{ $badgeType->name }}</strong> contributor badge on the Africa Health Knowledge Hub has grown stronger.</p>

<p>You now have <strong>{{ number_format($lifetimeContributions) }}</strong> lifetime contributions across published resources, forum threads, and comments on the hub.</p>

<p><a href="{{ $profileUrl }}">View your public contributor profile</a></p>

<p>Thank you for sharing knowledge that strengthens public health across Africa.</p>
