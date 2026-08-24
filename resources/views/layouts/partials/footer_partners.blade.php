@php $footerPartners = footer_partner_logos(); @endphp
@if(count($footerPartners) > 0)
<style>
.footer-partners { padding: 0.9rem 0 0.35rem; border-top: 1px solid rgba(255,255,255,.12); }
.light-footer .footer-partners,
.footer.bg-body-secondary .footer-partners { border-top-color: rgba(15,23,42,.08); padding-top: 1.1rem; }
.footer-partners-label {
    display: block;
    text-align: center;
    font-size: .7rem;
    font-weight: 600;
    letter-spacing: .08em;
    text-transform: uppercase;
    opacity: .7;
    margin-bottom: .55rem;
}
.footer-partners-list {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    gap: .75rem 1.1rem;
}
.footer-partners-item {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 44px;
    padding: .35rem .7rem;
    background: rgba(255,255,255,.94);
    border-radius: 6px;
}
.footer-partners-item img {
    max-height: 32px;
    max-width: 110px;
    width: auto;
    height: auto;
    object-fit: contain;
}
</style>
<div class="footer-partners" id="khub-footer-partners">
    <span class="footer-partners-label">{{ __('ui_body.footer_partners') }}</span>
    <div class="footer-partners-list">
        @foreach($footerPartners as $partner)
            @if($partner['url'] !== '')
                <a class="footer-partners-item" href="{{ $partner['url'] }}" target="_blank" rel="noopener noreferrer" title="{{ $partner['name'] }}">
                    <img src="{{ $partner['image'] }}" alt="{{ $partner['name'] !== '' ? $partner['name'] : 'Partner' }}">
                </a>
            @else
                <span class="footer-partners-item" title="{{ $partner['name'] }}">
                    <img src="{{ $partner['image'] }}" alt="{{ $partner['name'] !== '' ? $partner['name'] : 'Partner' }}">
                </span>
            @endif
        @endforeach
    </div>
</div>
@endif
