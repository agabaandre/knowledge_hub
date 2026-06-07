@php
    $defaultImage = asset('assets/images/cover.png');
    $image = $row->image_url ?? $defaultImage;
@endphp
<div class="card col-lg-12 single-border mb-2 publication-list-card federation-publication-card">
    <div class="card-body text-left">
        <div class="mb-2">
            @include('partials.federation.source_badge', ['item' => $row, 'hubName' => $row->federation_hub_name ?? null])
        </div>
        <div class="row publication-card-row" style="display:flex;flex-wrap:nowrap;align-items:stretch;">
            <div class="col-md-3 publication-image-col" style="min-height:150px;overflow:hidden;display:flex;align-items:center;justify-content:center;width:35%;flex:0 0 35%;max-width:35%;">
                <a href="{{ $row->federation_source_url }}" target="_blank" rel="noopener noreferrer" class="publication-image-link" style="display:block;width:100%;height:100%;">
                    <img src="{{ $image }}" alt="{{ clean_unicode($row->title ?? '') }}" class="publication-image"
                         style="width:100%;height:100%;min-height:150px;object-fit:contain;"
                         onerror="this.onerror=null;this.src='{{ $defaultImage }}';">
                </a>
            </div>
            <div class="col-md-9 publication-content-col" style="width:65%;flex:1 1 65%;max-width:65%;padding-left:1rem;">
                <h5 class="text-bold text-lg publication-title-desktop">
                    <a href="{{ $row->federation_source_url }}" target="_blank" rel="noopener noreferrer">
                        {!! truncate(clean_unicode($row->title ?? ''), 500) !!}
                    </a>
                </h5>
                <p class="text-muted" style="text-align:justify;">
                    {!! Str::words(strip_tags(clean_unicode($row->description ?? '')), 40, '...') !!}
                </p>
                @if(!empty($row->federation_country))
                    <span class="muted medium d-block"><i class="fa fa-map-marker-alt me-1"></i>{{ $row->federation_country }}</span>
                @endif
                <div class="d-flex align-items-center mt-2" style="gap:0.5rem;flex-wrap:wrap;">
                    <a href="{{ $row->federation_source_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm fed-btn-primary">
                        <i class="fa fa-external-link me-1"></i>Open on {{ $row->federation_hub_name }}
                    </a>
                    <a href="{{ route('federation.browse', ['hub' => $row->federation_hub_id]) }}" class="btn btn-sm fed-btn-outline">
                        More from this hub
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
