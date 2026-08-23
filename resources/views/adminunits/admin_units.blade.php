@php
    $primary = settings()->primary_color ?? '#119A48';
@endphp
<style>
    .adminunit-grid { margin-left: -12px; margin-right: -12px; }
    .adminunit-card {
        --adminunit-accent: {{ $primary }};
        position: relative;
        display: flex;
        flex-direction: column;
        height: 100%;
        padding: 1.35rem 1.2rem 1.2rem;
        background: #fff;
        border: 1px solid #e8eef5;
        border-radius: 20px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
        text-decoration: none !important;
        color: inherit;
        overflow: hidden;
        transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
    }
    .adminunit-card::before {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--adminunit-accent), #16c653);
    }
    .adminunit-card:hover,
    .adminunit-card:focus {
        transform: translateY(-6px);
        box-shadow: 0 18px 36px rgba(15, 23, 42, 0.12);
        border-color: var(--adminunit-accent);
        color: inherit;
    }
    .adminunit-card:hover .adminunit-card__icon {
        transform: scale(1.06);
    }
    .adminunit-card__media {
        position: relative;
        width: 72px;
        height: 72px;
        margin-bottom: .95rem;
        flex-shrink: 0;
    }
    .adminunit-card__icon {
        width: 72px;
        height: 72px;
        border-radius: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(145deg, var(--adminunit-accent) 0%, #16c653 100%);
        color: #fff;
        font-size: 1.75rem;
        box-shadow: 0 12px 22px rgba(17, 154, 72, 0.28);
        transition: transform .22s ease;
    }
    .adminunit-card__logo {
        position: absolute;
        right: -8px;
        bottom: -6px;
        width: 28px;
        height: 28px;
        border-radius: 999px;
        object-fit: cover;
        border: 2px solid #fff;
        box-shadow: 0 4px 10px rgba(15, 23, 42, 0.16);
        background: #fff;
    }
    .adminunit-card__name {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.35;
        margin: 0 0 .4rem;
    }
    .adminunit-card__desc {
        font-size: .84rem;
        color: #64748b;
        line-height: 1.5;
        margin: 0 0 .75rem;
    }
    .adminunit-card__footer {
        margin-top: auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
    }
    .adminunit-card__meta {
        font-size: .75rem;
        font-weight: 600;
        color: var(--adminunit-accent);
        background: rgba(17, 154, 72, 0.08);
        border-radius: 999px;
        padding: .22rem .7rem;
    }
    .adminunit-card__arrow {
        margin-left: auto;
        color: #94a3b8;
        font-size: .85rem;
        transition: transform .22s ease, color .22s ease;
    }
    .adminunit-card:hover .adminunit-card__arrow {
        color: var(--adminunit-accent);
        transform: translateX(3px);
    }
    .adminunit-card--static {
        cursor: default;
    }
    .adminunit-card--static:hover {
        transform: none;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
    }
</style>
<div class="row adminunit-grid align-items-stretch justify-content-center">
    @forelse($adminunits as $unit)
        @php
            $iconClass = trim((string) ($unit->icon ?: 'fa-building'));
            if ($iconClass !== '' && ! str_starts_with($iconClass, 'fa')) {
                $iconClass = 'fa '.$iconClass;
            } elseif (! str_contains($iconClass, ' ')) {
                $iconClass = 'fa '.$iconClass;
            }
            $hasLogo = ! empty($unit->logo);
            $childCount = (int) ($unit->children_count ?? 0);
        @endphp
        <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-6 mb-4" data-aos="fade-up">
            <a href="{{ url('adminunits/details') }}?id={{ $unit->id }}" class="adminunit-card">
                <span class="adminunit-card__media">
                    <span class="adminunit-card__icon" aria-hidden="true">
                        <i class="{{ $iconClass }}"></i>
                    </span>
                    @if($hasLogo)
                        <img class="adminunit-card__logo" src="{{ storage_link('uploads/adminunits/'.$unit->logo) }}" alt="">
                    @endif
                </span>
                <h3 class="adminunit-card__name">{{ truncate($unit->name, 50) }}</h3>
                @if(! empty($unit->description))
                    <p class="adminunit-card__desc">{{ \Illuminate\Support\Str::limit(strip_tags((string) $unit->description), 90) }}</p>
                @endif
                <span class="adminunit-card__footer">
                    @if($childCount > 0)
                        <span class="adminunit-card__meta">{{ $childCount }} {{ $childCount === 1 ? 'subunit' : 'subunits' }}</span>
                    @endif
                    <i class="fa fa-arrow-right adminunit-card__arrow" aria-hidden="true"></i>
                </span>
            </a>
        </div>
    @empty
        @unless(!empty($hideEmpty))
            <div class="col-12 text-center text-muted py-5">No administrative units yet.</div>
        @endunless
    @endforelse
</div>
