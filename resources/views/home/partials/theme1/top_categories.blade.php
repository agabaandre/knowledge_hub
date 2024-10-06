<section class="py-5" style="background-color: #34495e;">
    <div class="container" id="categorization">
        <div class="row justify-content-center">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="crp_box fl_color ovr_top">
                    <div class="row align-items-center">
                        @foreach ($categories as $category)
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 mb-4">
                                <a href="{{ url($category['link']) }}" class="text-decoration-none">
                                    <div class="dro_140 card shadow-lg"
                                        style="border-radius: 20px; transition: transform 0.3s;">
                                        <div class="dro_141 de text-center"
                                            style="padding: 30px; background-color: #e74c3c; border-radius: 20px 20px 0 0;">
                                            <i class="{{ $category['icon'] }}"
                                                style="font-size: 3em; color: #ecf0f1;"></i>
                                        </div>
                                        <div class="dro_142 text-center" style="padding: 20px;">
                                            <h6 style="font-weight: bold; color: #ecf0f1;">{{ $category['title'] }}</h6>
                                            <p style="color: #bdc3c7;">{{ $category['stats'] }} Records</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
