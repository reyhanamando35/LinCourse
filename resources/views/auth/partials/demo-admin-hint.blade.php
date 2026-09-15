{{-- Kredensial akun demo admin untuk pengunjung portofolio. Akun ini read-only (DemoReadOnlyMiddleware). --}}
<div class="p-3 text-sm text-blue-900 bg-blue-50 border border-blue-200 rounded-lg">
    <p class="font-semibold">Want to try the admin panel?</p>
    <p class="mt-1">Sign in with this demo admin account:</p>
    <dl class="mt-2 grid grid-cols-[auto_1fr] gap-x-2 gap-y-1">
        <dt class="text-blue-700">Email</dt>
        <dd class="font-mono text-xs sm:text-sm break-all">{{ config('app.demo_admin_email') }}</dd>
        <dt class="text-blue-700">Password</dt>
        <dd class="font-mono text-xs sm:text-sm">password123</dd>
    </dl>
    <button type="button" id="use-demo-admin"
        class="mt-3 w-full px-3 py-1.5 text-xs font-medium text-blue-700 bg-white border border-blue-300 rounded-lg hover:bg-blue-100">
        Fill in demo admin account
    </button>
    <p class="mt-2 text-xs text-blue-700">Demo admin can view everything, but actions that change data are disabled.</p>
</div>
<script>
    document.getElementById('use-demo-admin').addEventListener('click', function () {
        document.getElementById('email').value = @json(config('app.demo_admin_email'));
        document.getElementById('password').value = 'password123';
    });
</script>
