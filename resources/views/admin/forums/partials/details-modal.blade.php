<div class="modal" id="details{{$forum->id}}">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="max-height: 100vh;">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel">Forum Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
            </div>
            <div class="modal-body" style="overflow-y: auto;">
               @include('forums.partials.forum_details',['no_comments'=>true])
            </div>
            <div class="modal-footer">
                <!-- Toogle to second dialog -->
                @if($forum->is_approved == 0)
                 <a href="{{ url('admin/forums/approve') }}?id={{$forum->id}}" class="btn btn-outline-success">Approve & Post</a>
                 @else
                 <a href="{{ url('admin/forums/reject') }}?id={{$forum->id}}" class="btn btn-outline-danger">Reject Forum</a>
                @endif
            </div>
        </div>
    </div>
</div>