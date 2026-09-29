<!-- PWA Primary Meta Tags -->
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#064e3b">
<meta name="mobile-web-app-capable" content="yes">
<meta name="application-name" content="Elvith">

<!-- Apple / iOS Specific Meta Tags -->
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Elvith">
<link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
<link rel="apple-touch-icon" sizes="152x152" href="/icons/icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="/icons/apple-touch-icon.png">
<link rel="apple-touch-icon" sizes="192x192" href="/icons/icon-192x192.png">

<!-- Service Worker Registration & PWA Install Prompt Handler -->
<script>
    // Global PWA Install Prompt State
    window.deferredPwaPrompt = null;
    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent default mini-infobar on mobile Chrome
        e.preventDefault();
        // Stash the event so it can be triggered later
        window.deferredPwaPrompt = e;
        console.log('[PWA] beforeinstallprompt captured!');
        // Dispatch custom event for UI banners
        window.dispatchEvent(new CustomEvent('pwa-installable'));
    });

    window.addEventListener('appinstalled', () => {
        window.deferredPwaPrompt = null;
        console.log('[PWA] App installed successfully');
    });

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/service-worker.js', { scope: '/' })
                .then(reg => {
                    console.log('[PWA] ServiceWorker registered:', reg.scope);
                })
                .catch(err => {
                    console.warn('[PWA] ServiceWorker registration error:', err);
                });
        });
    }
</script>
