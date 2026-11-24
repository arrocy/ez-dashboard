<aside class="dashboard-sidebar" id="dashboardSidebar">
    <div class="d-flex align-items-center px-4 py-4">
        <a href="{{ route('welcome') }}" class="d-flex align-items-center gap-2 fw-bold fs-5">
            <x-avatar :src="setting('company_avatar')" :name="setting('company_name', 'Arrocy')" />
            <span class="sidebar-section-label">{{ setting('company_name', 'Arrocy') }}</span>
        </a>
    </div>

    @foreach (config('menu.sidebar') as $heading => $menu)
        @if (empty($menu['roles']) || Auth::user()->hasRole($menu['roles']))
        <div class="sidebar-section-label px-4 pt-3 pb-2 text-uppercase small fw-semibold">{{ $heading ?? '' }}</div>
        @foreach($menu['items'] ?? [] as $itemIndex => $item)
            @if (empty($item['roles']) || Auth::user()->hasRole($item['roles']))
            <nav class="nav flex-column">
                @if (!empty($item['children']))
                @php
                    $submenuId = 'sidebar-submenu-' . $heading . '-' . $itemIndex;
                    $isExpanded = request()->routeIs($item['route']) || collect($item['children'])->contains(fn ($child) => request()->routeIs($child['route']));
                @endphp
                <button
                    class="nav-link {{ $isExpanded ? 'active' : '' }}"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#{{ $submenuId }}"
                    title="{{ $item['label'] }}"
                    aria-label="{{ $item['label'] }}"
                    aria-expanded="{{ $isExpanded ? 'true' : 'false' }}"
                    aria-controls="{{ $submenuId }}"
                >
                    <i class="{{ $item['icon'] }}"></i>
                    <span class="sidebar-label">{{ $item['label'] }}</span>
                    <i class="fa-solid fa-chevron-down ms-auto small"></i>
                </button>
                <div class="collapse {{ $isExpanded ? 'show' : '' }}" id="{{ $submenuId }}">
                    <nav class="nav flex-column ms-3">
                        @foreach ($item['children'] as $child)
                            @if (empty($child['roles']) || Auth::user()->hasRole($child['roles']))
                            <a class="nav-link {{ request()->routeIs($child['route']) ? 'active' : '' }}" href="{{ route($child['route']) }}" title="{{ $child['label'] }}" aria-label="{{ $child['label'] }}">
                                <i class="{{ $child['icon'] }}" aria-hidden="true"></i>
                                <span class="sidebar-label">{{ $child['label'] }}</span>
                            </a>
                            @endif
                        @endforeach
                    </nav>
                </div>
                @else
                <a class="nav-link {{ request()->routeIs($item['route']) ? 'active' : '' }}" href="{{ route($item['route']) }}" title="{{ $item['label'] }}" aria-label="{{ $item['label'] }}">
                    <i class="{{ $item['icon'] }}" aria-hidden="true"></i>
                    <span class="sidebar-label">{{ $item['label'] }}</span>
                </a>
                @endif
            </nav>
            @endif
        @endforeach
        @endif
    @endforeach
    {{-- <div class="sidebar-section-label px-4 pt-3 pb-2 text-uppercase small fw-semibold">{{ setting('company_name', 'Arrocy') . ' v' . config('app.version') }}</div> --}}
</aside>
