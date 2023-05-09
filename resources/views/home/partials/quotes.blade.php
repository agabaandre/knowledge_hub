

<div class="quotes-slider px-1">
<!-- single review -->
@foreach($quotes as $quote)
    <div class="single_review">
        <div class="reviews_wrap position-relative rounded py-1 px-2">
            <div class="rw-header d-flex mt-3 text-bold">
                <p class="text-bold text-white">{{nl2br($quote->quote)}}</p>
            </div>
        </div>
    </div>
@endforeach

</div>