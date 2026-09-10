@php
    /** @var \App\Models\Publication|object $publication */
    $publication = $publication ?? $row ?? null;
    if (! $publication) {
        return;
    }

    $citationLinks = \App\Support\PublicationCitationMeta::citationLinks($publication);
    if ($citationLinks === []) {
        return;
    }

    $btnClass = $btnClass ?? 'btn btn-sm btn-outline-secondary';
@endphp
<details class="publication-citations-details" onclick="event.stopPropagation();">
    <summary class="{{ $btnClass }}" title="Look up citations in external indexes">
        <i class="fa fa-quote-right me-1" aria-hidden="true"></i> Citations
    </summary>
    <div class="publication-citations-details__menu" role="group" aria-label="Track citations">
        <div class="publication-citations-details__hint">Track citations (opens in a new tab)</div>
        @foreach($citationLinks as $citeLink)
            <a class="publication-citations-details__link"
               href="{{ $citeLink['url'] }}"
               target="_blank"
               rel="noopener noreferrer">
                {{ $citeLink['label'] }}
                <i class="fa fa-external-link-alt" style="font-size: 0.7rem;" aria-hidden="true"></i>
            </a>
        @endforeach
    </div>
</details>
