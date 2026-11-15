<div class="auth-google">
    <a class="auth-google-button" href="{{ route('auth.google.redirect') }}">
        <svg width="20" height="20" viewBox="0 0 48 48" aria-hidden="true" focusable="false">
            <path fill="#4285F4" d="M43.61 24.46c0-1.36-.12-2.66-.35-3.92H24v7.43h11a9.4 9.4 0 0 1-4.08 6.16v5.13h6.6c3.86-3.56 6.09-8.8 6.09-14.8Z"/>
            <path fill="#34A853" d="M24 44c5.51 0 10.13-1.83 13.51-4.96l-6.6-5.13c-1.83 1.23-4.17 1.96-6.91 1.96-5.32 0-9.83-3.59-11.44-8.41H5.75v5.28A20.4 20.4 0 0 0 24 44Z"/>
            <path fill="#FBBC05" d="M12.56 27.46a12.22 12.22 0 0 1 0-7.72v-5.28H5.75a20.39 20.39 0 0 0 0 18.28l6.81-5.28Z"/>
            <path fill="#EA4335" d="M24 11.33c3 0 5.69 1.03 7.81 3.06l5.86-5.86A19.65 19.65 0 0 0 24 3.2 20.4 20.4 0 0 0 5.75 14.46l6.81 5.28c1.61-4.82 6.12-8.41 11.44-8.41Z"/>
        </svg>
        <span>{{ $label }}</span>
    </a>
    <div class="auth-google-divider"><span>hoặc dùng email</span></div>
</div>
