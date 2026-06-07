<div id="khub-login-i18n">
    <div class="text-center mb-4 notranslate">
        <h2 class="m-0 ft-regular">{{ __('ui_body.login_modal_title') }}</h2>
    </div>

    <form action="{{ route('login') }}" method="post">
        @csrf

        <input type="hidden" name="route" value="front" />
        <div class="form-group notranslate">
            <label>{{ __('ui_body.username') }}</label>
            <input type="email" name="email" class="form-control" placeholder="Username*"
                autocomplete="off">
        </div>

        <div class="form-group notranslate">
            <label>{{ __('ui_body.password') }}</label>
            <input type="password" name="password" class="form-control" placeholder="Password*"
                autocomplete="off">
        </div>

        <div class="form-group notranslate">
            <div class="d-flex align-items-center justify-content-between">
                <div class="flex-1">
                    <input id="dd" class="checkbox-custom" name="remember" type="checkbox">
                    <label for="dd" class="checkbox-custom-label">{{ __('ui_body.remember_me') }}</label>
                </div>
                <div class="eltio_k2">
                    <a href="#" class="theme-cl">{{ __('ui_body.lost_password') }}</a>
                </div>
            </div>
        </div>

        <div class="form-group notranslate">
            <button type="submit"
                class="btn btn-md full-width theme-bg text-light fs-md ft-medium">{{ __('ui_body.login_submit') }}</button>
        </div>

    </form>
</div>
