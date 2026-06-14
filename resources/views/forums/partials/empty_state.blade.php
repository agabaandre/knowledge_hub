<div class="empty-state">
    <div class="empty-icon">
        <i class="fa fa-comments"></i>
    </div>
    <h3>No Discussions Yet</h3>
    <p class="text-muted">Be the first to start a discussion!</p>
    @auth
    <a href="{{ url('forums/create') }}" class="btn btn-sm theme-bg text-white mt-3">
        <i class="fa fa-plus-circle me-2"></i>Start First Discussion
    </a>
    @endauth
</div>
