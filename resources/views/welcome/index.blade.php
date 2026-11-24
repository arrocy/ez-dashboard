@extends('layouts.landing')

@section('title', 'Arrocy EZ Dashboard | Your workspace, your way')

@section('content')
<nav class="navbar navbar-expand-lg bg-body py-3 border-bottom">
    <div class="container">
        <a class="d-flex align-items-center gap-2 text-decoration-none text-body fw-bold" href="{{ route('welcome') }}">
            <x-avatar :src="setting('company_avatar')" name="Arrocy EZ Dashboard" />
            <span>Arrocy <span class="fw-normal text-body-secondary">EZ Dashboard</span></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#siteNav" aria-controls="siteNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="siteNav">
            <ul class="navbar-nav mx-auto gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="#platform">Platform</a></li>
                <li class="nav-item"><a class="nav-link" href="#modules">Modules</a></li>
                <li class="nav-item"><a class="nav-link" href="#workflow">How it works</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">Go to dashboard <i class="fa-solid fa-arrow-right ms-2"></i></a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-link text-body text-decoration-none">Sign in</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Create account</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<main class="welcome-page">
    <section class="welcome-hero py-5 py-lg-6">
        <div class="container py-4 py-lg-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-5">
                    <span class="welcome-eyebrow"><span class="welcome-eyebrow-dot"></span> A MODULAR ADMIN WORKSPACE</span>
                    <h1 class="display-4 fw-bold lh-sm mt-4 mb-4">Your tools. Your workflow. <span class="welcome-heading-accent">One dashboard.</span></h1>
                    <p class="lead text-body-secondary mb-4">Arrocy EZ Dashboard brings your day-to-day admin into one clear workspace, with optional modules you can add as your needs grow.</p>
                    <div class="d-flex flex-wrap gap-3">
                        @auth
                            <a class="btn btn-primary btn-lg" href="{{ route('dashboard') }}">Open dashboard <i class="fa-solid fa-arrow-right ms-2"></i></a>
                        @else
                            <a class="btn btn-primary btn-lg" href="{{ route('register') }}">Get started <i class="fa-solid fa-arrow-right ms-2"></i></a>
                        @endauth
                        <a class="btn btn-outline-secondary btn-lg" href="#modules">Explore the modules</a>
                    </div>
                    <div class="d-flex flex-wrap gap-4 mt-4 small text-body-secondary">
                        <span><i class="fa-solid fa-check text-primary me-2"></i>Start with the essentials</span>
                        <span><i class="fa-solid fa-check text-primary me-2"></i>Add features when you need them</span>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="welcome-dashboard" aria-label="Preview of the Arrocy EZ Dashboard admin panel">
                        <aside class="welcome-dashboard-sidebar">
                            <div class="d-flex align-items-center gap-2 mb-5">
                                <span class="welcome-dashboard-mark"><i class="fa-solid fa-cubes-stacked"></i></span>
                                <span class="fw-semibold">Arrocy <span class="fw-normal text-white-50">EZ</span></span>
                            </div>
                            <div class="welcome-sidebar-label">WORKSPACE</div>
                            <div class="welcome-sidebar-link active"><i class="fa-solid fa-chart-pie"></i>Overview</div>
                            <div class="welcome-sidebar-link"><i class="fa-solid fa-users"></i>Users</div>
                            <div class="welcome-sidebar-label mt-4">ADD-ON MODULES</div>
                            <div class="welcome-sidebar-link"><i class="fa-brands fa-whatsapp"></i>WhatsApp</div>
                            <div class="welcome-sidebar-link"><i class="fa-solid fa-address-book"></i>Phonebook</div>
                            <div class="welcome-sidebar-link"><i class="fa-brands fa-openai"></i>AI Bot</div>
                            <div class="welcome-sidebar-link"><i class="fa-solid fa-file-lines"></i>Templates</div>
                        </aside>
                        <div class="welcome-dashboard-content">
                            <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
                                <div>
                                <div class="small text-body-secondary">ADMIN WORKSPACE</div>
                                    <h2 class="h4 fw-bold mb-0 mt-1">Workspace overview</h2>
                                </div>
                                <span class="welcome-admin-avatar">A</span>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-4">
                                    <div class="welcome-stat">
                                        <span class="welcome-stat-icon"><i class="fa-solid fa-users"></i></span>
                                        <div class="small text-body-secondary mt-3">Team members</div>
                                        <div class="h4 fw-bold mb-0">12</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="welcome-stat">
                                        <span class="welcome-stat-icon welcome-stat-icon-purple"><i class="fa-solid fa-puzzle-piece"></i></span>
                                        <div class="small text-body-secondary mt-3">Modules</div>
                                        <div class="h4 fw-bold mb-0">04 <span class="small fw-normal text-body-secondary">active</span></div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="welcome-stat">
                                        <span class="welcome-stat-icon welcome-stat-icon-green"><i class="fa-solid fa-circle-check"></i></span>
                                        <div class="small text-body-secondary mt-3">System status</div>
                                        <div class="fw-bold">All good</div>
                                    </div>
                                </div>
                            </div>
                            <div class="welcome-module-panel">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="fw-semibold">Your modules</div>
                                    <span class="small text-primary fw-semibold">Manage modules <i class="fa-solid fa-arrow-right ms-1"></i></span>
                                </div>
                                <div class="welcome-module-row">
                                    <span class="welcome-module-icon text-success"><i class="fa-brands fa-whatsapp"></i></span>
                                    <span class="flex-grow-1 fw-medium">WhatsApp</span>
                                    <span class="welcome-module-status"><span></span>Active</span>
                                </div>
                                <div class="welcome-module-row">
                                    <span class="welcome-module-icon text-primary"><i class="fa-solid fa-address-book"></i></span>
                                    <span class="flex-grow-1 fw-medium">Phonebook</span>
                                    <span class="welcome-module-status"><span></span>Active</span>
                                </div>
                                <div class="welcome-module-row">
                                    <span class="welcome-module-icon text-warning"><i class="fa-solid fa-wand-magic-sparkles"></i></span>
                                    <span class="flex-grow-1 fw-medium">AI Bot</span>
                                    <span class="welcome-module-status"><span></span>Active</span>
                                </div>
                                <div class="welcome-module-row">
                                    <span class="welcome-module-icon text-info"><i class="fa-solid fa-file-lines"></i></span>
                                    <span class="flex-grow-1 fw-medium">Templates</span>
                                    <span class="welcome-module-status"><span></span>Active</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="platform" class="py-5 py-lg-6">
        <div class="container py-4">
            <div class="row align-items-end g-4 mb-5">
                <div class="col-lg-7">
                    <span class="welcome-section-label">A BETTER WAY TO ADMIN</span>
                    <h2 class="display-6 fw-bold mt-2 mb-0">Everything connected. Nothing in the way.</h2>
                </div>
                <div class="col-lg-5">
                    <p class="text-body-secondary mb-0">A dependable foundation for managing your platform, with the flexibility to make it your own as you grow.</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <article class="welcome-feature-card h-100">
                        <span class="welcome-feature-icon"><i class="fa-solid fa-puzzle-piece"></i></span>
                        <h3 class="h5 fw-bold mt-4">Modular by design</h3>
                        <p class="text-body-secondary mb-0">Keep your workspace focused. Add the packages that fit your operation and leave out the ones you don't need.</p>
                    </article>
                </div>
                <div class="col-md-6 col-lg-4">
                    <article class="welcome-feature-card h-100">
                        <span class="welcome-feature-icon welcome-feature-icon-purple"><i class="fa-solid fa-sliders"></i></span>
                        <h3 class="h5 fw-bold mt-4">One familiar control center</h3>
                        <p class="text-body-secondary mb-0">Manage users, settings, and the tools your team relies on from one consistent admin experience.</p>
                    </article>
                </div>
                <div class="col-md-6 col-lg-4">
                    <article class="welcome-feature-card h-100">
                        <span class="welcome-feature-icon welcome-feature-icon-green"><i class="fa-solid fa-arrow-trend-up"></i></span>
                        <h3 class="h5 fw-bold mt-4">Room to grow</h3>
                        <p class="text-body-secondary mb-0">Begin with the core dashboard, then extend your setup with purpose-built add-ons whenever you're ready.</p>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section id="modules" class="welcome-modules py-5 py-lg-6">
        <div class="container py-4">
            <div class="text-center mx-auto mb-5" style="max-width: 44rem">
                <span class="welcome-section-label">EXTEND YOUR WORKSPACE</span>
                <h2 class="display-6 fw-bold mt-2">The right modules for the work you do.</h2>
                <p class="text-body-secondary mb-0">Choose from Arrocy add-ons to shape a workspace around your team's needs.</p>
            </div>
            <div class="row g-4">
                <div class="col-sm-6 col-lg-3">
                    <article class="welcome-addon-card h-100">
                        <span class="welcome-addon-icon text-success"><i class="fa-brands fa-whatsapp"></i></span>
                        <h3 class="h5 fw-bold mt-4">WhatsApp</h3>
                        <p class="text-body-secondary mb-0">Bring WhatsApp tools into your admin workspace with a dedicated add-on.</p>
                    </article>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <article class="welcome-addon-card h-100">
                        <span class="welcome-addon-icon text-primary"><i class="fa-solid fa-address-book"></i></span>
                        <h3 class="h5 fw-bold mt-4">Phonebook</h3>
                        <p class="text-body-secondary mb-0">Keep your contact management close to the rest of your team's workflow.</p>
                    </article>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <article class="welcome-addon-card h-100">
                        <span class="welcome-addon-icon text-warning"><i class="fa-solid fa-wand-magic-sparkles"></i></span>
                        <h3 class="h5 fw-bold mt-4">AI Bot</h3>
                        <p class="text-body-secondary mb-0">Extend your setup with AI-powered tools for your platform.</p>
                    </article>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <article class="welcome-addon-card h-100">
                        <span class="welcome-addon-icon text-info"><i class="fa-solid fa-file-lines"></i></span>
                        <h3 class="h5 fw-bold mt-4">Templates</h3>
                        <p class="text-body-secondary mb-0">Add reusable templates to help standardize your team's setup.</p>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section id="workflow" class="py-5 py-lg-6">
        <div class="container py-4">
            <div class="welcome-cta d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
                <div>
                    <span class="welcome-section-label">READY WHEN YOU ARE</span>
                    <h2 class="h2 fw-bold mt-2 mb-2">Build the workspace that works for you.</h2>
                    <p class="text-body-secondary mb-0">Start with Arrocy EZ Dashboard and add modules on your terms.</p>
                </div>
                <div class="d-flex flex-wrap gap-3 flex-shrink-0">
                    @auth
                        <a class="btn btn-primary btn-lg" href="{{ route('dashboard') }}">Go to dashboard <i class="fa-solid fa-arrow-right ms-2"></i></a>
                    @else
                        <a class="btn btn-primary btn-lg" href="{{ route('register') }}">Create your account <i class="fa-solid fa-arrow-right ms-2"></i></a>
                        <a class="btn btn-outline-secondary btn-lg" href="{{ route('login') }}">Sign in</a>
                    @endauth
                </div>
            </div>
        </div>
    </section>
</main>

<footer class="welcome-footer py-4 border-top">
    <div class="container d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <a class="d-flex align-items-center gap-2 text-decoration-none text-body fw-bold" href="{{ route('welcome') }}">
            <x-avatar :src="setting('company_avatar')" name="Arrocy EZ Dashboard" />
            <span>Arrocy EZ Dashboard</span>
        </a>
        <span class="small text-body-secondary">&copy; {{ date('Y') }} Arrocy. A flexible admin workspace.</span>
        <div class="d-flex gap-3 small">
            <a class="text-body-secondary text-decoration-none" href="#platform">Platform</a>
            <a class="text-body-secondary text-decoration-none" href="#modules">Modules</a>
            @auth
                <a class="text-body-secondary text-decoration-none" href="{{ route('dashboard') }}">Dashboard</a>
            @else
                <a class="text-body-secondary text-decoration-none" href="{{ route('login') }}">Sign in</a>
            @endauth
        </div>
    </div>
</footer>
@endsection
