<div class="row">
    @include('account.partials.contributor_public_profile_stats', ['contributorPublicProfile' => $contributorPublicProfile ?? null])

    @if(!$user->is_social_login)
    <div class="col-lg-5 mb-4">
        <div class="card account-profile-shell h-100">
            <div class="card-header">
                <h3 class="card-title">Security</h3>
                <p class="account-profile-shell__subtitle">Update your account password.</p>
            </div>
            <form class="form-horizontal" method="post" action="{{ $password_route ?? route('account.auth_update') }}">
                @csrf
                <div class="card-body">
                    <div class="account-profile-field">
                        <label class="form-label" for="old_pass">Current password</label>
                        <input type="password" class="form-control" id="old_pass" placeholder="Current password" name="old_pass">
                    </div>
                    <div class="account-profile-field">
                        <label class="form-label" for="password">New password</label>
                        <input type="password" class="form-control" id="password" placeholder="New password" name="password">
                    </div>
                    <div class="account-profile-field mb-0">
                        <label class="form-label" for="password_confirmation">Confirm new password</label>
                        <input type="password" class="form-control" id="password_confirmation" placeholder="Confirm new password"
                            name="password_confirmation">
                    </div>
                </div>
                <div class="card-footer text-right bg-white border-top">
                    <button type="submit" class="btn btn-success">Update password</button>
                </div>
            </form>
        </div>
    </div>
    @else
    <div class="col-12 mb-3">
        <div class="card account-profile-shell">
            <div class="card-header">
                <h3 class="card-title">Greetings {{ $user->first_name ? $user->first_name : $user->name }}</h3>
            </div>
            <div class="card-body">
                @php
                    $ssoLabel = $user->social_provider
                        ? \App\Support\OAuthAccountSecurity::providerDisplayName($user->social_provider)
                        : 'your SSO provider';
                @endphp
                <div class="alert alert-info mb-0">
                    <h5 class="mb-2"><i class="fa fa-info-circle me-2"></i>Account managed by {{ $ssoLabel }}</h5>
                    <p class="mb-0">Your account is signed in with {{ $ssoLabel }}. Password changes are managed in your {{ $ssoLabel }} account settings, not on this site.</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="{{ $user->is_social_login ? 'col-12' : 'col-lg-7' }} mb-4">
        <div class="card account-profile-shell">
            <div class="card-header">
                <h3 class="card-title">Personal information</h3>
                <p class="account-profile-shell__subtitle">Keep your profile up to date for better recommendations and community matching.</p>
            </div>
            <form class="form-horizontal" method="post" enctype="multipart/form-data"
                action="{{ $update_route ?? route('account.update') }}">
                @csrf
                <div class="card-body">
                    @php
                        $image_link = $user->photo ? $user->photo : asset('assets/images/user.jpg');
                    @endphp

                    <div class="account-profile-photo">
                        <div class="account-profile-photo__preview" id="profilePhotoPreviewWrap">
                            <img src="{{ $image_link }}" alt="Profile photo" id="profilePhotoPreview">
                        </div>
                        <div class="account-profile-photo__actions">
                            <label class="btn btn-outline-success btn-sm mb-2" for="profilePhotoInput">
                                <i class="fa fa-camera me-1"></i> Upload photo
                            </label>
                            <input type="file" name="photo" id="profilePhotoInput" accept="image/jpeg,image/png,image/gif,image/webp" class="d-none">
                            <p class="small text-muted mb-0">JPG, PNG, GIF or WebP · max 5 MB</p>
                            @error('photo')
                                <div class="text-danger small mt-1"><strong>{{ $message }}</strong></div>
                            @enderror
                        </div>
                    </div>

                    <input type="hidden" name="id" value="{{ $user->id }}" />

                    <h4 class="account-profile-section-title">Preferences</h4>

                    <div class="account-profile-field">
                        <label class="form-label">Language</label>
                        @include('common.lang')
                    </div>

                    @if(\Illuminate\Support\Facades\Schema::hasColumn('users', 'theme_preference'))
                    <div class="account-profile-field">
                        <label class="form-label" for="theme_preference">Theme</label>
                        <select name="theme_preference" id="theme_preference" class="form-control">
                            <option value="light" @if(($user->theme_preference ?? 'light') === 'light') selected @endif>Light</option>
                            <option value="dark" @if(($user->theme_preference ?? '') === 'dark') selected @endif>Dark</option>
                            <option value="system" @if(($user->theme_preference ?? '') === 'system') selected @endif>System (follow device)</option>
                        </select>
                        <small class="text-muted">Choose light or dark theme for the site. Saved with your profile.</small>
                    </div>
                    @endif

                    <h4 class="account-profile-section-title">Contact &amp; role</h4>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="account-profile-field">
                                <label class="form-label" for="first_name">First name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First name"
                                    value="{{ $user->first_name }}" {{ $user->is_social_login ? 'readonly' : '' }}>
                                @error('first_name')
                                    <div class="text-danger small mt-1"><strong>{{ $message }}</strong></div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="account-profile-field">
                                <label class="form-label" for="last_name">Last name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last name"
                                    value="{{ $user->last_name }}" {{ $user->is_social_login ? 'readonly' : '' }}>
                                @error('last_name')
                                    <div class="text-danger small mt-1"><strong>{{ $message }}</strong></div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="account-profile-field">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" class="form-control" id="email" placeholder="Email"
                            value="{{ $user->email }}" name="email" required
                            {{ $user->is_social_login ? 'readonly' : '' }}>
                        @error('email')
                            <div class="text-danger small mt-1"><strong>{{ $message }}</strong></div>
                        @enderror
                    </div>

                    <div class="account-profile-field">
                        <label class="form-label" for="phone_number">Phone number</label>
                        <input type="tel" class="form-control" id="phone_number" placeholder="Phone number"
                            name="phone_number" value="{{ $user->phone_number }}" inputmode="tel" autocomplete="tel">
                        @error('phone_number')
                            <div class="text-danger small mt-1"><strong>{{ $message }}</strong></div>
                        @enderror
                    </div>

                    @php
                        $availableJobNames = \App\Models\JobTitle::query()->pluck('name')->map(function ($name) {
                            return trim((string) $name);
                        })->toArray();
                        $currentJobTitle = trim((string) ($user->job_title ?? ''));
                        $isCurrentJobMissing = $currentJobTitle !== '' && !in_array($currentJobTitle, $availableJobNames, true);
                        $showCustomJobField = old('job_missing') || $isCurrentJobMissing;
                    @endphp
                    <div class="account-profile-field">
                        <label class="form-label">Job title</label>
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
                            <input type="text" class="form-control camel-case-input" placeholder="Job title (optional)"
                                name="job_title_custom" value="{{ old('job_title_custom', $isCurrentJobMissing ? $currentJobTitle : '') }}" id="job_title_custom_account">
                            <small class="text-muted">Optional if your title is not listed.</small>
                        </div>
                        @error('job_title')
                            <div class="text-danger small mt-1"><strong>{{ $message }}</strong></div>
                        @enderror
                        @error('job_title_custom')
                            <div class="text-danger small mt-1"><strong>{{ $message }}</strong></div>
                        @enderror
                    </div>

                    <div class="account-profile-field">
                        <label class="form-label" for="organization_name">Organization / institution</label>
                        <input type="text" class="form-control camel-case-input" id="organization_name" placeholder="Organization / institution name"
                            name="organization_name" value="{{ $user->organization_name }}">
                        @error('organization_name')
                            <div class="text-danger small mt-1"><strong>{{ $message }}</strong></div>
                        @enderror
                    </div>

                    <div class="account-profile-field">
                        <label class="form-label" for="orcid">ORCID</label>
                        <input type="text" class="form-control" id="orcid" placeholder="0000-0000-0000-0000"
                            name="orcid" value="{{ $user->orcid }}" maxlength="19">
                        <small class="form-text text-muted">Optional · 19 characters, format 0000-0000-0000-0000</small>
                        @error('orcid')
                            <div class="text-danger small mt-1"><strong>{{ $message }}</strong></div>
                        @enderror
                    </div>

                    <h4 class="account-profile-section-title">Interests &amp; communities</h4>

                    <div class="account-profile-field">
                        <label class="form-label">Your interests</label>
                        @include('partials.publications.subtheme_dropdown', [
                            'field' => 'preferences[]',
                            'multiple' => 'multiple',
                            'selected' => $preferences,
                        ])
                        @error('preferences')
                            <div class="text-danger small mt-1"><strong>{{ $message }}</strong></div>
                        @enderror
                    </div>

                    <div class="account-profile-field">
                        <label class="form-label">Preferred communities</label>
                        @include('partials.publications.publication_communities_dropdown', [
                            'field' => 'communities[]',
                            'selected' => @$user->communities ? $user->communities : [],
                        ])
                        @error('communities')
                            <div class="text-danger small mt-1"><strong>{{ $message }}</strong></div>
                        @enderror
                    </div>

                    @can('alter_access_levels')
                    <div class="account-profile-field">
                        <label class="form-label" for="level_id">Access level</label>
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
                    @endcan

                    <h4 class="account-profile-section-title">Location &amp; updates</h4>

                    <div class="account-profile-field">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="is_subscribed" id="is_subscribed"
                                value="1" {{ $user->is_subscribed ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_subscribed">
                                Subscribe to monthly updates and newsletters
                            </label>
                        </div>
                        @error('is_subscribed')
                            <div class="text-danger small mt-1"><strong>{{ $message }}</strong></div>
                        @enderror
                    </div>

                    <div class="account-profile-field mb-0">
                        <label class="form-label">Country *</label>
                        @include('partials.countries.dropdown', [
                            'field' => 'country_id',
                            'selected' => old('country_id', $user->country_id),
                        ])
                        <small class="form-text text-muted">Please keep your country up to date for better recommendations and community matching.</small>
                        @error('country_id')
                            <div class="text-danger small mt-1"><strong>{{ $message }}</strong></div>
                        @enderror
                    </div>
                </div>

                <div class="card-footer text-right bg-white border-top">
                    <button type="submit" class="btn btn-success">Update profile</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
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

    document.querySelectorAll('.camel-case-input').forEach(function(input) {
        input.addEventListener('blur', function() {
            if (this.value && this.value.trim() !== '') {
                this.value = toTitleCase(this.value.trim());
            }
        });
        input.addEventListener('paste', function() {
            setTimeout(function() {
                if (input.value && input.value.trim() !== '') {
                    input.value = toTitleCase(input.value.trim());
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

    var photoInput = document.getElementById('profilePhotoInput');
    var photoPreview = document.getElementById('profilePhotoPreview');
    if (photoInput && photoPreview) {
        photoInput.addEventListener('change', function () {
            var file = this.files && this.files[0];
            if (!file) return;
            if (!file.type.startsWith('image/')) {
                alert('Please choose an image file (JPG, PNG, GIF, or WebP).');
                this.value = '';
                return;
            }
            var reader = new FileReader();
            reader.onload = function (event) {
                photoPreview.src = event.target.result;
            };
            reader.readAsDataURL(file);
        });
    }
})();
</script>
