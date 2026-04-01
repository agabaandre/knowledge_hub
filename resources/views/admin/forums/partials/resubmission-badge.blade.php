@if((int) ($forum->is_resubmission_pending ?? 0) === 1)
    <span class="badge badge-warning text-dark {{ $class ?? '' }}" title="Author resubmitted after a previous rejection">Resubmission</span>
@endif
