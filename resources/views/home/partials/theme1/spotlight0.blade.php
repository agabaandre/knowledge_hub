<div class="spotlight px-3 py-5" style="background: linear-gradient(135deg, #2c3e50, #34495e); color: #ecf0f1;">
    <form action="{{ url('records/search') }}" class="filters mx-auto" style="max-width: 800px;">
        <div class="row no-gutters bg-dark rounded search-form shadow-lg" id="simple_search" style="overflow: hidden;">
            <div class="col-12">
                <div class="form-group mb-0 position-relative main_search">
                    <input type="text" class="form-control left-ico autocomplete term main-search" name="term"
                        value="{{ @old('term') }}" placeholder="Type Keywords"
                        style="font-size: 1.5em; border-radius: 30px; padding: 15px; transition: all 0.3s ease;" />
                </div>
            </div>
            <div class="col-12 mt-3">
                <div class="form-group mb-0 position-relative text-center">
                    <button class="btn btn-lg theme-bg text-white fs-md py-3" type="submit"
                        style="border-radius: 30px; background-color: #e74c3c; border: none; transition: background-color 0.3s ease;">
                        <i class="fa fa-search"></i> Search
                    </button>
                </div>
            </div>
        </div>
        @include('partials.search.advanced_search')
    </form>
    <div class="row spot-row col-sm-12 d-flex align-items-center mt-5">
        <div class="col-lg-4 col-md-12 col-sm-12 px-3">
            @include('home.partials.quotes')
        </div>
        <div class="col-lg-8 col-md-12 col-sm-12 pb-5" style="z-index: 100;">
            <div class="row justify-content-center mb-2">
                <h3 class="text-center" style="font-size: 24px; color: #ecf0f1;">Select a public health theme of
                    interest to find resources</h3>
            </div>
            @include('home.partials.theme_tabs')
        </div>
    </div>
</div>
