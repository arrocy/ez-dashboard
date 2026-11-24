@php
    $providers = collect([
        [
            'key' => 'google',
            'enabled' => (bool) setting('socialite_google_enabled'),
            'label' => 'Google',
            'icon' => 'fa-brands fa-google',
        ],
        [
            'key' => 'facebook',
            'enabled' => (bool) setting('socialite_facebook_enabled'),
            'label' => 'Facebook',
            'icon' => 'fa-brands fa-facebook-f',
        ],
        [
            'key' => 'instagram',
            'enabled' => (bool) setting('socialite_instagram_enabled'),
            'label' => 'Instagram',
            'icon' => 'fa-brands fa-instagram',
        ],
        [
            'key' => 'x',
            'enabled' => (bool) setting('socialite_x_enabled'),
            'label' => 'X',
            'icon' => 'fa-brands fa-x-twitter',
        ],
    ])->filter(fn ($provider) => $provider['enabled']);
@endphp

@if ($providers->isNotEmpty())
    <div class="auth-social-login mb-4">
        <div class="row {{ $providers->count() > 1 ? 'row-cols-2' : '' }} g-2">
            @foreach ($providers as $provider)
                <div class="col">
                    <a
                        href="{{ route('login.socialite', $provider['key']) }}"
                        class="btn btn-light social-login-button w-100"
                        aria-label="{{ __('Continue with :provider', ['provider' => $provider['label']]) }}"
                    >
                        <i class="{{ $provider['icon'] }}"></i>
                        {{--<x-social-icon :name="$provider['key']" />--}}
                        <span>{{ $provider['label'] }}</span>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="auth-divider" role="separator">
            <span>{{ __('Or continue with email') }}</span>
        </div>
    </div>
@endif
