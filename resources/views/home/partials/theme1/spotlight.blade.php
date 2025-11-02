<!-- Intro Banner
================================================== -->
<!-- add class "disable-gradient" to enable consistent background overlay -->
<style>
.intro-banner {
    opacity: 1 !important;
    background-color: rgba(255, 255, 255, 0.95) !important;
}

.intro-banner::before {
    background: rgba(255, 255, 255, 0.7) !important;
    opacity: 0.9 !important;
}

.intro-banner::after {
    opacity: 0.3 !important;
    background: linear-gradient(135deg, rgba(17, 154, 72, 0.1) 0%, rgba(22, 198, 83, 0.1) 100%) !important;
}

.banner-headline {
    opacity: 1 !important;
    color: #333 !important;
}

.banner-headline * {
    color: #333 !important;
    opacity: 1 !important;
}
</style>
<div data-background-image="{{ settings()->spotlight_banner }}" class="intro-banner disable-gradient">
    <div class="container">

        <!-- Intro Headline -->
        <div class="row">
            <div class="col-md-12">
                <div class="banner-headline text-dark">
                    @include('home.partials.quotes')
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="row">
            <form action="{{ url('records/search') }}" class="col-md-12">
                <div class="intro-banner-search-form margin-top-50">

                    <!-- Search Field -->
                    <div class="intro-search-field">
                        <div class="input-with-icon">
                            <input id="autocomplete-input" type="search" placeholder="Type Keyword to search">
                            <i class="icon-material-outline-search"></i>
                        </div>
                    </div>

                    <!-- Button -->
                    <div class="intro-search-button">
                        <button class="button ripple-effect" type="submit">Search</button>
                    </div>
                </div>
                @include('partials.search.advanced_search')

            </form>

        </div>
        <!-- Theme Tabs -->
        <div class="row mt-3 px-2 justify-content-center">
            @include('home.partials.theme_tabs')
        </div>
        @if(settings()->show_quotes ?? false)
            <div class="row mt-2 px-2">
                @include('home.partials.quotes')
            </div>
        @endif

        

    </div>
</div>
