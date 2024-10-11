<!-- Intro Banner
================================================== -->
<!-- add class "disable-gradient" to enable consistent background overlay -->
<div data-background-image="{{ settings()->spotlight_banner }}" class="intro-banner">
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
                            <input id="autocomplete-input" type="text" placeholder="Type Keyword to search">
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
        <!-- Stats -->
        <div class="row mt-3 px-2 justify-content-center">
            @include('home.partials.theme_tabs')
        </div>

    </div>
</div>
