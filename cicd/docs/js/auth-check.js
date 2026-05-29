/**
 * CI/CD Auth Check Script
 * 
 * Include this script at the top of protected pages to enforce authentication.
 * 
 * Usage in HTML:
 * <script src="js/auth-check.js" data-require-role="deployer"></script>
 * 
 * Roles: admin, deployer, viewer
 * - viewer: Can view all pages
 * - deployer: Can view and trigger deployments
 * - admin: Full access including settings
 */

(function() {
    'use strict';
    
    // Calculate paths relative to the HTML document, not the script
    const basePath = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/') + 1);
    const LOGIN_PAGE = basePath + 'login.html';
    const API_BASE = basePath + 'api/';
    
    // Get required role from script tag attribute
    const scriptTag = document.currentScript;
    const requiredRole = scriptTag?.getAttribute('data-require-role') || 'viewer';
    
    // Role hierarchy for permission checking
    // viewer: basic view access, support: support team access, deployer: can trigger deployments, admin: full access
    const roleHierarchy = { viewer: 1, support: 1.5, deployer: 2, admin: 3 };
    
    function hasPermission(userRole, requiredRole) {
        const userLevel = roleHierarchy[userRole] || 0;
        const requiredLevel = roleHierarchy[requiredRole] || 0;
        return userLevel >= requiredLevel;
    }
    
    function redirectToLogin() {
        const currentPage = encodeURIComponent(window.location.href);
        window.location.href = `${LOGIN_PAGE}?redirect=${currentPage}`;
    }
    
    function showAccessDenied() {
        document.body.innerHTML = `
            <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:100vh;font-family:Inter,sans-serif;background:#f5f5f7;color:#1f2937;">
                <div style="background:#fff;padding:40px;border-radius:16px;text-align:center;box-shadow:0 10px 40px rgba(0,0,0,0.1);">
                    <div style="font-size:4rem;margin-bottom:16px;">🚫</div>
                    <h1 style="font-size:1.5rem;margin-bottom:8px;">Access Denied</h1>
                    <p style="color:#6b7280;margin-bottom:24px;">You don't have permission to access this page.</p>
                    <a href="${LOGIN_PAGE}" style="display:inline-block;padding:12px 24px;background:#7c3aed;color:#fff;border-radius:8px;text-decoration:none;font-weight:500;">Sign In</a>
                    <a href="javascript:history.back()" style="display:inline-block;padding:12px 24px;background:#e5e7eb;color:#1f2937;border-radius:8px;text-decoration:none;font-weight:500;margin-left:8px;">Go Back</a>
                </div>
            </div>
        `;
    }
    
    async function checkAuth() {
        // First check sessionStorage for cached user
        const cachedUser = sessionStorage.getItem('cicd_user');
        if (cachedUser) {
            try {
                const user = JSON.parse(cachedUser);
                if (hasPermission(user.role, requiredRole)) {
                    // User has permission, allow page to render
                    applyRoleVisibility(user.role);
                    return;
                } else {
                    showAccessDenied();
                    return;
                }
            } catch(e) {
                // Invalid cached data, continue to API check
            }
        }
        
        // Check with server
        try {
            const res = await fetch(API_BASE + 'auth.php?action=check', {
                credentials: 'include'
            });
            const data = await res.json();
            
            if (!data.success || !data.authenticated) {
                redirectToLogin();
                return;
            }
            
            // Cache user info
            sessionStorage.setItem('cicd_user', JSON.stringify(data.user));
            
            // Check permission
            if (!hasPermission(data.user.role, requiredRole)) {
                showAccessDenied();
                return;
            }
            
            // Apply visibility rules for UI elements
            applyRoleVisibility(data.user.role);
            
            // User is authenticated and has permission
            // Dispatch event for pages that want to know when auth is complete
            window.dispatchEvent(new CustomEvent('cicd:auth-ready', { detail: data.user }));
            
        } catch(err) {
            console.warn('Auth API unavailable, using dev mode');
            // DEV MODE: If API is unavailable, create a mock admin session for development
            const devUser = { username: 'Admin', role: 'admin', dev_mode: true };
            sessionStorage.setItem('cicd_user', JSON.stringify(devUser));
            applyRoleVisibility('admin');
            window.dispatchEvent(new CustomEvent('cicd:auth-ready', { detail: devUser }));
        }
    }

    function applyRoleVisibility(userRole) {
        // Run after DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => applyRoleVisibility(userRole));
            return;
        }

        // Hide elements that require a higher role than the user has
        document.querySelectorAll('[data-require-role]').forEach(el => {
            const required = el.getAttribute('data-require-role');
            if (required && !hasPermission(userRole, required)) {
                el.style.display = 'none';
            }
        });

        // Shortcut for admin-only elements
        if (userRole !== 'admin') {
            document.querySelectorAll('[data-admin-only="true"]').forEach(el => {
                el.style.display = 'none';
            });
        }
    }

    
    // Run auth check immediately
    checkAuth();
    
    // Export helper functions globally
    window.cicdAuth = {
        getUser: () => {
            const cached = sessionStorage.getItem('cicd_user');
            return cached ? JSON.parse(cached) : null;
        },
        logout: async () => {
            try {
                await fetch(API_BASE + 'auth.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ action: 'logout' }),
                    credentials: 'include'
                });
            } catch(e) {}
            sessionStorage.removeItem('cicd_user');
            window.location.href = LOGIN_PAGE;
        },
        hasRole: (role) => {
            const user = window.cicdAuth.getUser();
            return user ? hasPermission(user.role, role) : false;
        }
    };
})();
