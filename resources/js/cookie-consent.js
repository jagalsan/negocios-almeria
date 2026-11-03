const STORAGE_KEY = 'na_cookie_consent';

const consentRoot = document.querySelector('[data-cookie-consent]');

if (consentRoot) {
    const version = consentRoot.dataset.version;
    const banner = consentRoot.querySelector('[data-cookie-banner]');
    const dialog = consentRoot.querySelector('[data-cookie-dialog]');
    const analyticsInput = consentRoot.querySelector('[data-cookie-analytics]');
    const marketingInput = consentRoot.querySelector('[data-cookie-marketing]');
    const gtmId = consentRoot.dataset.gtmId?.trim();
    const gaId = consentRoot.dataset.gaId?.trim();
    let trackingLoaded = false;

    const gtag = (...args) => {
        window.dataLayer = window.dataLayer || [];
        if (typeof window.gtag === 'function') window.gtag(...args);
        else window.dataLayer.push(args);
    };

    const readConsent = () => {
        try {
            const value = JSON.parse(localStorage.getItem(STORAGE_KEY));
            return value?.version === version ? value : null;
        } catch {
            return null;
        }
    };

    const clearAnalyticsCookies = () => {
        document.cookie.split(';').forEach((cookie) => {
            const name = cookie.split('=')[0].trim();
            if (name === '_ga' || name.startsWith('_ga_') || name === '_gid') {
                document.cookie = `${name}=; Max-Age=0; path=/; SameSite=Lax`;
                document.cookie = `${name}=; Max-Age=0; path=/; domain=.${location.hostname}; SameSite=Lax`;
            }
        });
    };

    const loadTracking = () => {
        if (trackingLoaded) return;
        trackingLoaded = true;

        if (/^GTM-[A-Z0-9]+$/i.test(gtmId || '')) {
            window.dataLayer.push({ 'gtm.start': Date.now(), event: 'gtm.js' });
            const script = document.createElement('script');
            script.async = true;
            script.src = `https://www.googletagmanager.com/gtm.js?id=${encodeURIComponent(gtmId)}`;
            document.head.appendChild(script);
            return;
        }

        if (/^G-[A-Z0-9]+$/i.test(gaId || '')) {
            const script = document.createElement('script');
            script.async = true;
            script.src = `https://www.googletagmanager.com/gtag/js?id=${encodeURIComponent(gaId)}`;
            document.head.appendChild(script);
            gtag('js', new Date());
            gtag('config', gaId, { anonymize_ip: true });
        }
    };

    const applyConsent = (consent) => {
        gtag('consent', 'update', {
            analytics_storage: consent.analytics ? 'granted' : 'denied',
            ad_storage: consent.marketing ? 'granted' : 'denied',
            ad_user_data: consent.marketing ? 'granted' : 'denied',
            ad_personalization: consent.marketing ? 'granted' : 'denied',
        });

        const hasTagManager = /^GTM-[A-Z0-9]+$/i.test(gtmId || '');
        if (consent.analytics || (consent.marketing && hasTagManager)) loadTracking();
        if (!consent.analytics) clearAnalyticsCookies();
    };

    const saveConsent = (analytics, marketing) => {
        const consent = { version, analytics, marketing, savedAt: new Date().toISOString() };
        localStorage.setItem(STORAGE_KEY, JSON.stringify(consent));
        applyConsent(consent);
        consentRoot.hidden = true;
        if (dialog.open) dialog.close();
        window.dispatchEvent(new CustomEvent('cookie-consent:updated', { detail: consent }));
    };

    const openSettings = () => {
        const consent = readConsent();
        analyticsInput.checked = Boolean(consent?.analytics);
        marketingInput.checked = Boolean(consent?.marketing);
        consentRoot.hidden = false;
        banner.hidden = true;
        if (typeof dialog.showModal === 'function') dialog.showModal();
        else dialog.setAttribute('open', '');
    };

    consentRoot.querySelectorAll('[data-cookie-accept]').forEach((button) => button.addEventListener('click', () => saveConsent(true, true)));
    consentRoot.querySelectorAll('[data-cookie-reject]').forEach((button) => button.addEventListener('click', () => saveConsent(false, false)));
    consentRoot.querySelector('[data-cookie-save]').addEventListener('click', () => saveConsent(analyticsInput.checked, marketingInput.checked));
    consentRoot.querySelector('[data-cookie-customize]').addEventListener('click', openSettings);
    consentRoot.querySelector('[data-cookie-close]').addEventListener('click', () => {
        if (dialog.open) dialog.close();
        consentRoot.hidden = Boolean(readConsent());
        banner.hidden = false;
    });
    document.querySelectorAll('[data-cookie-settings]').forEach((button) => button.addEventListener('click', openSettings));

    const consent = readConsent();
    if (consent) {
        applyConsent(consent);
    } else {
        consentRoot.hidden = false;
        banner.hidden = false;
    }
}
