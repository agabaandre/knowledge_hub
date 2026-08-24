@php
    $footerPartners = footer_partner_logos();
    $showPartnerNames = \App\Support\FooterPartners::showNames(function_exists('settings') ? settings() : null);
@endphp
@if(count($footerPartners) > 0)
<style>
.footer-partners {
    background: #ffffff;
    padding: 1.1rem 0 1rem;
    border-top: 1px solid #e8edf2;
    border-bottom: 1px solid #e8edf2;
}
.footer-partners-label {
    display: block;
    text-align: center;
    font-size: .7rem;
    font-weight: 600;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: #64748b;
    margin-bottom: .7rem;
}
.footer-partners-list {
    display: flex;
    justify-content: center;
    align-items: {{ $showPartnerNames ? 'flex-start' : 'center' }};
    flex-wrap: wrap;
    gap: .9rem 1.4rem;
}
.footer-partners-item {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 51px;
    padding: .2rem .35rem;
    text-decoration: none;
}
.footer-partners-item img {
    max-height: 37px;
    max-width: 127px;
    width: auto;
    height: auto;
    object-fit: contain;
}
.footer-partners-name {
    display: block;
    margin-top: .35rem;
    font-size: .68rem;
    font-weight: 600;
    line-height: 1.25;
    color: #475569;
    text-align: center;
    max-width: 127px;
}
</style>
<div class="footer-partners" id="khub-footer-partners">
    <div class="container">
        <span class="footer-partners-label">{{ __('ui_body.footer_partners') }}</span>
        <div class="footer-partners-list">
            @foreach($footerPartners as $partner)
                @if($partner['url'] !== '')
                    <a class="footer-partners-item" href="{{ $partner['url'] }}" target="_blank" rel="noopener noreferrer" title="{{ $partner['name'] }}">
                        <img src="{{ $partner['image'] }}" alt="{{ $partner['name'] !== '' ? $partner['name'] : 'Partner' }}">
                        @if($showPartnerNames && $partner['name'] !== '')
                            <span class="footer-partners-name">{{ $partner['name'] }}</span>
                        @endif
                    </a>
                @else
                    <span class="footer-partners-item" title="{{ $partner['name'] }}">
                        <img src="{{ $partner['image'] }}" alt="{{ $partner['name'] !== '' ? $partner['name'] : 'Partner' }}">
                        @if($showPartnerNames && $partner['name'] !== '')
                            <span class="footer-partners-name">{{ $partner['name'] }}</span>
                        @endif
                    </span>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endif
