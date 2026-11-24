@if (setting('body_footer_enabled', false))
<footer class="dashboard-footer border-top px-3 px-lg-4 py-1 small">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="text-nowrap">
            <span class="mx-1">&copy;{{ now()->year }} {{ setting('company_name', 'Laravel') }} v{{ config('app.version', '1.0.0') }}</span>
        </div>
        <div class="text-nowrap">
            <a href="https://wa.me/{{ setting('company_phone') }}" target="_blank" rel="noopener noreferrer">Support</a>
        </div>
        <div class="text-nowrap">
            Laravel {{ app()->version() }} <span class="mx-1">|</span> PHP {{ PHP_VERSION }}
        </div>
    </div>
</footer>
@endif
