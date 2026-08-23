<style>
    .guide-hero { text-align: center; padding: 2rem 0; }
    .guide-hero h1 { font-size: 2rem; font-weight: 700; margin: 0 0 .5rem; color: #fff; }
    .guide-hero p { margin: 0; color: rgba(255,255,255,.95); }
    .guide-wrap { max-width: 1040px; margin: 0 auto 3rem; }
    .guide-card { background: #fff; border: 1px solid #e2e8f0; border-radius: .25rem; padding: 1.5rem 1.75rem 2rem; }
    .guide-card h1 { font-size: 1.6rem; margin-top: 0; }
    .guide-card h2 { font-size: 1.25rem; margin-top: 2rem; padding-top: .75rem; border-top: 1px solid #edf2f7; }
    .guide-card h3 { font-size: 1.05rem; margin-top: 1.25rem; }
    .guide-card p, .guide-card li { color: #4a5568; line-height: 1.7; }
    .guide-card a { color: var(--theme-color-primary, #119A48); }
    .guide-card table { width: 100%; border-collapse: collapse; margin: 1rem 0; font-size: .95rem; }
    .guide-card th, .guide-card td { border: 1px solid #e2e8f0; padding: .5rem .65rem; text-align: left; vertical-align: top; }
    .guide-card th { background: #f8fafc; }
    .guide-card code, .guide-card pre { background: #f1f5f9; border-radius: 4px; }
    .guide-card code { padding: .1rem .35rem; font-size: .875em; }
    .guide-card pre { padding: .85rem 1rem; overflow: auto; }
    .guide-card pre code { padding: 0; background: transparent; }
    .guide-card ul { padding-left: 1.2rem; }
    .guide-card img {
        display: block;
        width: 100%;
        max-width: 100%;
        height: auto;
        margin: 1.1rem 0 .4rem;
        border: 1px solid #e2e8f0;
        border-radius: .4rem;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .08);
        background: #f8fafc;
    }
    .guide-card p:has(> img) { margin-bottom: 0; }
    .guide-card p:has(> img) + p { margin-top: .25rem; color: #64748b; font-size: .9rem; }
    .guide-switch { margin-bottom: 1rem; }
    html[data-bs-theme="dark"] .guide-card { background: #242628; border-color: #3e4348; }
    html[data-bs-theme="dark"] .guide-card p, html[data-bs-theme="dark"] .guide-card li { color: #d1d5db; }
    html[data-bs-theme="dark"] .guide-card h1, html[data-bs-theme="dark"] .guide-card h2, html[data-bs-theme="dark"] .guide-card h3 { color: #e4e6eb; }
    html[data-bs-theme="dark"] .guide-card th, html[data-bs-theme="dark"] .guide-card td { border-color: #3e4348; }
    html[data-bs-theme="dark"] .guide-card th { background: #2d3136; }
    html[data-bs-theme="dark"] .guide-card h2 { border-top-color: #3e4348; }
    html[data-bs-theme="dark"] .guide-card code, html[data-bs-theme="dark"] .guide-card pre { background: #1f2326; }
    html[data-bs-theme="dark"] .guide-card img { border-color: #3e4348; background: #1f2326; box-shadow: none; }
    html[data-bs-theme="dark"] .guide-card p:has(> img) + p { color: #9ca3af; }
</style>
