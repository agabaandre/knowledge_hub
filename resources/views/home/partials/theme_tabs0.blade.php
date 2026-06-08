<style>
    .theme-grid {
        background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.4)), url('/path/to/bg.jpg') center/cover no-repeat;
        padding: 4rem 0;
        min-height: 100vh;
        display: flex;
        align-items: center;
    }

    .theme-card {
        background: #ffffff;
        border-radius: 0.25rem;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        min-height: 160px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        opacity: 0.8;
    }

    .theme-card:hover {
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        transform: translateY(-4px);
    }

    .theme-icon {
        font-size: 2rem;
        color: #119A48;
        margin-bottom: 0.75rem;
    }

    .theme-title {
        font-size: 1rem;
        font-weight: 600;
        color: #333;
        line-height: 1.3;
    }

    @media (max-width: 767px) {
        .theme-title {
            font-size: 0.95rem;
        }
    }
</style>

<section class="theme-grid">
    <div class="container">
        <h3 class="text-center text-white mb-5 font-weight-bold">Click a health theme below to access associated content</h3>
        <div class="row justify-content-center">
            @foreach ($themes as $theme)
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 mb-4 d-flex justify-content-center">
                    <a href="{{ thematic_area_records_url($theme) }}" class="w-100 text-decoration-none">
                        <div class="theme-card">
                            <div class="theme-icon">
                                <i class="fa {{ $theme->icon }}"></i>
                            </div>
                            <div class="theme-title" title="{{ $theme->description }}">
                                {{ truncate($theme->description, 22) }}
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
