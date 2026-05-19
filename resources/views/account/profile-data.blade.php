<div class="row">
    @if(!$user->is_social_login)
    <div class="col-md-5 col-lg-5 col-xl-5 col-xs-12 col-md-pull-2">

        <div class="card">
            <div class="card-header pb-0 border-bottom">
                <div class="item-user pro-user">
                    <h4 class="pro-user-username tx-15 pt-2 mt-1 mb-4">
                        Greetings {{ $user->first_name ? $user->first_name : $user->name }}
                    </h4>
                </div>
            </div>
            <form class="form-horizontal" method="post" action="{{ $password_route ?? route('account.auth_update') }}">
                @csrf
                <div class="card-body">

                    <h4 class=" pb-1">Change Password</h4>
                    <div class="form-group">
                        <label class="form-label">Old Password</label>
                        <input type="password" class="form-control" placeholder="Current Password" name="old_pass">
                    </div>
                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <input type="password" class="form-control" placeholder="New Password" name="password">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" placeholder=" Confirm New Password"
                            name="password_confirmation">
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
    @else
    <!-- SSO User Info -->
    <div class="col-md-12 col-lg-12 col-xl-12 mb-3">
        <div class="card">
            <div class="card-header pb-0 border-bottom">
                <div class="item-user pro-user">
                    <h4 class="pro-user-username tx-15 pt-2 mt-1 mb-4">
                        Greetings {{ $user->first_name ? $user->first_name : $user->name }}
                    </h4>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-0">
                    @php
                        $ssoLabel = $user->social_provider
                            ? \App\Support\OAuthAccountSecurity::providerDisplayName($user->social_provider)
                            : 'your SSO provider';
                    @endphp
                    <h5><i class="fa fa-info-circle me-2"></i>Account managed by {{ $ssoLabel }}</h5>
                    <p class="mb-0">Your account is signed in with {{ $ssoLabel }}. Password changes are managed in your {{ $ssoLabel }} account settings, not on this site.</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Col -->
    <div class="{{ $user->is_social_login ? 'col-md-12' : 'col-md-7 col-lg-7 col-xl-7' }}">
        <div class="card">
            <form class="form-horizontal" method="post" enctype="multipart/form-data"
                action="{{ $update_route ?? route('account.update') }}">
                @csrf
                <div class="card-body">
                    <div class="mb-4 ft-md">
                        <h4>Personal Information</h4>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Language</label>
                            </div>
                            <div class="col-md-9">
                                @include('common.lang')
                            </div>
                        </div>
                        <input type="hidden" name="id" value="{{ $user->id }}" />
                    </div>
                    @if(\Illuminate\Support\Facades\Schema::hasColumn('users', 'theme_preference'))
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Theme</label>
                            </div>
                            <div class="col-md-9">
                                <select name="theme_preference" class="form-control">
                                    <option value="light" @if(($user->theme_preference ?? 'light') === 'light') selected @endif>Light</option>
                                    <option value="dark" @if(($user->theme_preference ?? '') === 'dark') selected @endif>Dark</option>
                                    <option value="system" @if(($user->theme_preference ?? '') === 'system') selected @endif>System (follow device)</option>
                                </select>
                                <small class="text-muted">Choose light or dark theme for the site. Saved with your profile.</small>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">First Name</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="first_name" placeholder="First Name"
                                    value="{{ $user->first_name }}" {{ $user->is_social_login ? 'readonly' : '' }}>
                                @error('first_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label"> Last Name</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" name="last_name" class="form-control" placeholder="Last Name"
                                    value="{{ $user->last_name }}" {{ $user->is_social_login ? 'readonly' : '' }}>
                                @error('last_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Email</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control" placeholder="Email"
                                    value="{{ $user->email }}" name="email" required
                                    {{ $user->is_social_login ? 'readonly' : '' }}>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Phone Number</label>
                            </div>
                            <div class="col-md-9">
                                <input type="tel" class="form-control" placeholder="Phone number"
                                    name="phone_number" value="{{ $user->phone_number }}" inputmode="tel" autocomplete="tel">
                                @error('phone_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    @php
                        $availableJobNames = \App\Models\JobTitle::query()->pluck('name')->map(function ($name) {
                            return trim((string) $name);
                        })->toArray();
                        $currentJobTitle = trim((string) ($user->job_title ?? ''));
                        $isCurrentJobMissing = $currentJobTitle !== '' && !in_array($currentJobTitle, $availableJobNames, true);
                        $showCustomJobField = old('job_missing') || $isCurrentJobMissing;
                    @endphp
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Job Title</label>
                            </div>
                            <div class="col-md-9">
                                @include('partials.jobs.dropdown', [
                                    'field' => 'job',
                                    'selected' => old('job', $user->job_title),
                                    'valueField' => 'name',
                                ])
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="job_missing_account"
                                        name="job_missing" value="1" {{ $showCustomJobField ? 'checked' : '' }}>
                                    <label class="form-check-label" for="job_missing_account">
                                        My job title is missing from the list
                                    </label>
                                </div>
                                <div id="job_title_custom_wrap_account" class="mt-2" style="{{ $showCustomJobField ? '' : 'display:none;' }}">
                                    <input type="text" class="form-control camel-case-input" placeholder="Job Title (optional)"
                                        name="job_title_custom" value="{{ old('job_title_custom', $isCurrentJobMissing ? $currentJobTitle : '') }}" id="job_title_custom_account">
                                    <small class="text-muted">Optional if your title is not listed.</small>
                                </div>
                                @error('job_title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                @error('job_title_custom')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Organization / Institution</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control camel-case-input" placeholder="Organization / Institution Name"
                                    name="organization_name" value="{{ $user->organization_name }}" id="organization_name">
                                @error('organization_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">ORCID</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control" placeholder="ORCID ID (e.g., 0000-0000-0000-0000)"
                                    name="orcid" value="{{ $user->orcid }}" maxlength="19">
                                <small class="form-text text-muted">Optional: Your ORCID identifier (19 characters, format: 0000-0000-0000-0000)</small>
                                @error('orcid')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Your Interests</label>
                            </div>
                            <div class="col-md-9">
                                @include('partials.publications.subtheme_dropdown', [
                                    'field' => 'preferences[]',
                                    'multiple' => 'multiple',
                                    'selected' => $preferences,
                                ])
                                @error('preferences')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Preferred Communities</label>
                            </div>
                            <div class="col-md-9">
                                @include('partials.publications.publication_communities_dropdown', [
                                    'field' => 'communities[]',
                                    'selected' => @$user->communities ? $user->communities : [],
                                ])
                                @error('communities')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror


                            </div>
                        </div>
                    </div>
                    @can('alter_access_levels')
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Access Level</label>
                                </div>
                                <div class="col-md-9">
                                    <select class="form-control js-example-basic-single select2" name="level_id"
                                        id="level_id" required>
                                        @foreach ($access_groups as $group)
                                            <option {{ $user->access_level_id == $group->id ? 'selected' : '' }}
                                                value="{{ $group->id }}">
                                                {{ $group->level_name }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                        </div>
                    @endcan

                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Monthly Updates</label>
                            </div>
                            <div class="col-md-9">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="is_subscribed" id="is_subscribed" 
                                        value="1" {{ $user->is_subscribed ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_subscribed">
                                        Subscribe to monthly updates and newsletters
                                    </label>
                                </div>
                                @error('is_subscribed')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-2">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Country *</label>
                            </div>
                            <div class="col-md-9">
                                @include('partials.countries.dropdown', [
                                    'field' => 'country_id',
                                    'selected' => old('country_id', $user->country_id),
                                ])
                                <small class="form-text text-muted">Please keep your country up to date for better recommendations and community matching.</small>
                                @error('country_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group ">
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label class="form-label">Photo</label>
                            </div>
                            <div class="col-md-9">
                                <input type="file" name="photo" id="cover" />
                                @error('photo')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                @php
                                    if (@$user):
                                        $image_link = $user->photo;
                                    else:
                                        $image_link = asset('assets/images/user.jpg');
                                    endif;
                                @endphp
                                <div class="row justify-content-center">
                                    <div class="cover_preview py-2"
                                        style="min-height:120px; min-width:120px;max-height:120px; max-width:120px; background-image: url({{ $image_link }}); background-size:cover; background-position:center; background-repeat:no-repeat;">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-success waves-effect waves-light">Update Profile</button>
                </div>

            </form>
        </div>
    </div>
    <!-- /Col -->
</div>

<script>
(function() {
    // Title Case transformation function (capitalize first letter of each word)
    function toTitleCase(str) {
        if (!str) return '';
        return str
            .toLowerCase()
            .split(/\s+/)
            .map(function(word) {
                return word.charAt(0).toUpperCase() + word.slice(1);
            })
            .join(' ');
    }

    // Apply title case on blur
    document.querySelectorAll('.camel-case-input').forEach(function(input) {
        input.addEventListener('blur', function() {
            if (this.value && this.value.trim() !== '') {
                this.value = toTitleCase(this.value.trim());
            }
        });

        // Also apply on paste
        input.addEventListener('paste', function(e) {
            setTimeout(() => {
                if (this.value && this.value.trim() !== '') {
                    this.value = toTitleCase(this.value.trim());
                }
            }, 10);
        });
    });

    var missingCheckbox = document.getElementById('job_missing_account');
    var customWrap = document.getElementById('job_title_custom_wrap_account');
    var customInput = document.getElementById('job_title_custom_account');
    var dropdown = document.querySelector('select[name="job"]');

    function syncAccountJobInputs() {
        if (!missingCheckbox || !customWrap || !dropdown) return;
        if (missingCheckbox.checked) {
            customWrap.style.display = '';
            if (customInput && customInput.value.trim() === '') {
                var selectedOption = dropdown.options[dropdown.selectedIndex];
                var selectedText = selectedOption ? selectedOption.text.trim() : '';
                if (selectedText && selectedText.toLowerCase() !== 'select job') {
                    customInput.value = selectedText;
                }
            }
        } else {
            customWrap.style.display = 'none';
            if (customInput) customInput.value = '';
        }
    }

    if (missingCheckbox) {
        missingCheckbox.addEventListener('change', syncAccountJobInputs);
        syncAccountJobInputs();
    }
})();
</script>
