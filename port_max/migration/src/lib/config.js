/**
 * Dynamic API URL — auto-detects from browser domain.
 * Works for all mono-repo clients without rebuild.
 *
 * Production:  https://{client-domain}/port_max/api/index.php/
 * XAMPP direct: http://localhost/{subdir}/port_max/api/index.php/
 * Vite dev:    http://localhost/etail_development_src/port_max/api/index.php/
 */
function getApiUrl() {
    if (typeof window !== 'undefined') {
        const port = window.location.port;
        const isDevServer = port && port !== '80' && port !== '443';

        if (isDevServer) {
            // Vite/dev server (e.g. localhost:5173) — API lives on XAMPP at port 80
            return 'http://localhost/etail_development_src/port_max/api/index.php/';
        }

        // Production or XAMPP direct access — extract subdirectory from pathname
        const origin = window.location.origin;
        const path = window.location.pathname;
        const idx = path.indexOf('port_max');
        const basePath = idx > 0 ? path.substring(0, idx) : '/';
        return `${origin}${basePath}port_max/api/index.php/`;
    }
    // SSR / build-time fallback
    return 'http://localhost/etail_development_src/port_max/api/index.php/';
}

export const API_URL = getApiUrl();