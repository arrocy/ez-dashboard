@props(['name' => 'google', 'class' => null])

@php
    $providers = [
        'facebook' => [
            'icon' => 'fa-brands fa-facebook-f',
            'title' => 'Facebook',
            'class' => 'social-login-icon--facebook',
        ],
        'google' => [
            'icon' => 'fa-brands fa-google',
            'title' => 'Google',
            'class' => 'social-login-icon--google',
        ],
        'instagram' => [
            'icon' => 'fa-brands fa-instagram',
            'title' => 'Instagram',
            'class' => 'social-login-icon--instagram',
        ],
        'whatsapp' => [
            'icon' => 'fa-brands fa-whatsapp',
            'title' => 'WhatsApp',
            'class' => 'social-login-icon--whatsapp',
        ],
        'x' => [
            'icon' => 'fa-brands fa-x-twitter',
            'title' => 'X',
            'class' => 'social-login-icon--x',
        ],
    ];

    $provider = strtolower((string) $name);
    $config = $providers[$provider] ?? $providers['google'];
@endphp

<span
    {{ $attributes->class([
        'social-login-icon',
        $config['class'],
        $class,
    ]) }}
    title="{{ $config['title'] }}"
    aria-hidden="true"
>
    <i class="{{ $config['icon'] }}"></i>
</span>