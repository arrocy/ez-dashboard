@php
    $user = auth()->user();
@endphp
<header class="dashboard-topbar d-flex align-items-center justify-content-between gap-3 px-3 px-lg-4">
    <div class="d-flex align-items-center gap-3">
        <button
            type="button"
            class="btn btn-light border"
            data-sidebar-toggle
            aria-label="{{ __('Toggle compact sidebar') }}"
            aria-pressed="false"
            title="{{ __('Toggle compact sidebar') }}"
        >
            <i class="fa-solid fa-bars" aria-hidden="true"></i>
        </button>
        <span class="fw-semibold d-none d-sm-inline">@yield('page-heading', '')</span>
    </div>
    <div class="d-flex align-items-center gap-2">
        @impersonating($guard = null)
        <a href="{{ route('impersonate.leave') }}" class="btn btn-light border" title="{{ __('Leave Impersonating') }} {{ $user->name }}" aria-label="impersonate-{{ $user->name }}">
            <i class="fa-solid fa-user-secret" aria-hidden="true"></i>
        </a>
        @endImpersonating
        <button type="button" class="btn btn-light border" data-theme-toggle aria-label="Toggle dark or light theme" title="Toggle theme">
            <i class="fa-solid fa-circle-half-stroke"></i>
        </button>
        <div class="dropdown">
            <button class="btn btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-language me-1"></i>{{ \Helper::getLanguage(session('locale', 'en'))['name'] ?? 'English' }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                @foreach(\Helper::existingLangs() as $locale)
                @php
                    $language = \Helper::getLanguage($locale);
                @endphp
                <li>
                    <a href="{{ route('switch.language', $locale) }}" class="dropdown-item d-flex align-items-center gap-2" data-lang="{{ $locale }}" type="button">
                        <x-flag :code="$language['flag']" class="rounded-circle" />
                        <span>{{ $language['name'] }}</span>
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
        <div class="dropdown">
            <button class="btn btn-link text-decoration-none d-flex align-items-center gap-2 p-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <x-avatar :src="$user->avatar" :name="$user->name" size="2.25rem" />
                <span class="text-body fw-semibold d-none d-lg-inline">{{ $user->name }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="fa-regular fa-user me-2"></i>Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item text-danger" type="submit"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Log Out</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
