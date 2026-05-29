/**
 * Master Toaster Component - Logimax CI/CD
 * A beautiful, reusable toast notification system
 */

(function() {
    'use strict';

    // Create toast container if not exists
    function getToastContainer() {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            document.body.appendChild(container);
        }
        return container;
    }

    // Inject styles once
    function injectStyles() {
        if (document.getElementById('toast-styles')) return;
        
        const styles = document.createElement('style');
        styles.id = 'toast-styles';
        styles.textContent = `
            #toast-container {
                position: fixed;
                top: 24px;
                right: 24px;
                z-index: 9999;
                display: flex;
                flex-direction: column;
                gap: 12px;
                max-width: 400px;
                pointer-events: none;
            }

            .toast-notification {
                display: flex;
                align-items: flex-start;
                gap: 16px;
                padding: 16px 20px;
                border-radius: 16px;
                backdrop-filter: blur(10px);
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
                pointer-events: auto;
                transform: translateX(120%);
                opacity: 0;
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                position: relative;
                overflow: hidden;
            }

            .toast-notification.show {
                transform: translateX(0);
                opacity: 1;
            }

            .toast-notification.hide {
                transform: translateX(120%);
                opacity: 0;
            }

            /* Success Toast */
            .toast-notification.success {
                background: linear-gradient(135deg, rgba(187, 247, 208, 0.95) 0%, rgba(167, 243, 208, 0.9) 100%);
                border: 1px solid rgba(34, 197, 94, 0.2);
            }

            .toast-notification.success .toast-icon {
                background: white;
                color: #16a34a;
            }

            .toast-notification.success .toast-title {
                color: #14532d;
            }

            .toast-notification.success .toast-message {
                color: #166534;
            }

            /* Error Toast */
            .toast-notification.error {
                background: linear-gradient(135deg, rgba(254, 202, 202, 0.95) 0%, rgba(252, 165, 165, 0.9) 100%);
                border: 1px solid rgba(239, 68, 68, 0.2);
            }

            .toast-notification.error .toast-icon {
                background: white;
                color: #dc2626;
            }

            .toast-notification.error .toast-title {
                color: #7f1d1d;
            }

            .toast-notification.error .toast-message {
                color: #991b1b;
            }

            /* Warning Toast */
            .toast-notification.warning {
                background: linear-gradient(135deg, rgba(254, 243, 199, 0.95) 0%, rgba(253, 230, 138, 0.9) 100%);
                border: 1px solid rgba(245, 158, 11, 0.2);
            }

            .toast-notification.warning .toast-icon {
                background: white;
                color: #d97706;
            }

            .toast-notification.warning .toast-title {
                color: #78350f;
            }

            .toast-notification.warning .toast-message {
                color: #92400e;
            }

            /* Info Toast */
            .toast-notification.info {
                background: linear-gradient(135deg, rgba(207, 250, 254, 0.95) 0%, rgba(165, 243, 252, 0.9) 100%);
                border: 1px solid rgba(6, 182, 212, 0.2);
            }

            .toast-notification.info .toast-icon {
                background: white;
                color: #0891b2;
            }

            .toast-notification.info .toast-title {
                color: #164e63;
            }

            .toast-notification.info .toast-message {
                color: #155e75;
            }

            /* Decorative circle */
            .toast-notification::before {
                content: '';
                position: absolute;
                left: -20px;
                top: 50%;
                transform: translateY(-50%);
                width: 80px;
                height: 80px;
                border-radius: 50%;
                opacity: 0.3;
            }

            .toast-notification.success::before {
                background: #22c55e;
            }

            .toast-notification.error::before {
                background: #ef4444;
            }

            .toast-notification.warning::before {
                background: #f59e0b;
            }

            .toast-notification.info::before {
                background: #06b6d4;
            }

            .toast-icon {
                width: 44px;
                height: 44px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                font-size: 20px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                position: relative;
                z-index: 1;
            }

            .toast-content {
                flex: 1;
                min-width: 0;
                position: relative;
                z-index: 1;
            }

            .toast-title {
                font-size: 15px;
                font-weight: 600;
                margin-bottom: 4px;
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            }

            .toast-message {
                font-size: 13px;
                line-height: 1.4;
                opacity: 0.9;
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            }

            .toast-close {
                background: none;
                border: none;
                cursor: pointer;
                padding: 4px;
                color: inherit;
                opacity: 0.5;
                transition: opacity 0.2s;
                font-size: 18px;
                line-height: 1;
                position: relative;
                z-index: 1;
            }

            .toast-close:hover {
                opacity: 1;
            }

            /* Progress bar */
            .toast-progress {
                position: absolute;
                bottom: 0;
                left: 0;
                height: 3px;
                background: rgba(0, 0, 0, 0.15);
                border-radius: 0 0 16px 16px;
                transition: width linear;
            }

            @media (max-width: 480px) {
                #toast-container {
                    left: 16px;
                    right: 16px;
                    max-width: none;
                }
            }
        `;
        document.head.appendChild(styles);
    }

    // Icons for each type
    const icons = {
        success: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20,6 9,17 4,12"/></svg>',
        error: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
        warning: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
        info: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
    };

    /**
     * Show a toast notification
     * @param {Object} options - Toast options
     * @param {string} options.title - Toast title
     * @param {string} options.message - Toast message (optional)
     * @param {string} options.type - 'success' | 'error' | 'warning' | 'info'
     * @param {number} options.duration - Duration in ms (default 4000, 0 for persistent)
     */
    function showToast(options) {
        const { 
            title = 'Notification', 
            message = '', 
            type = 'info', 
            duration = 4000 
        } = options;

        injectStyles();
        const container = getToastContainer();

        const toast = document.createElement('div');
        toast.className = `toast-notification ${type}`;
        toast.innerHTML = `
            <div class="toast-icon">${icons[type] || icons.info}</div>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                ${message ? `<div class="toast-message">${message}</div>` : ''}
            </div>
            <button class="toast-close" aria-label="Close">×</button>
            ${duration > 0 ? '<div class="toast-progress"></div>' : ''}
        `;

        container.appendChild(toast);

        // Trigger animation
        requestAnimationFrame(() => {
            toast.classList.add('show');
        });

        // Progress bar animation
        if (duration > 0) {
            const progress = toast.querySelector('.toast-progress');
            if (progress) {
                progress.style.width = '100%';
                progress.style.transitionDuration = `${duration}ms`;
                requestAnimationFrame(() => {
                    progress.style.width = '0%';
                });
            }
        }

        // Close button
        const closeBtn = toast.querySelector('.toast-close');
        closeBtn.addEventListener('click', () => dismissToast(toast));

        // Auto dismiss
        if (duration > 0) {
            setTimeout(() => dismissToast(toast), duration);
        }

        return toast;
    }

    function dismissToast(toast) {
        if (!toast || toast.classList.contains('hide')) return;
        
        toast.classList.remove('show');
        toast.classList.add('hide');
        
        setTimeout(() => {
            toast.remove();
        }, 400);
    }

    // Shorthand methods
    window.Toast = {
        show: showToast,
        success: (title, message, duration) => showToast({ title, message, type: 'success', duration }),
        error: (title, message, duration) => showToast({ title, message, type: 'error', duration }),
        warning: (title, message, duration) => showToast({ title, message, type: 'warning', duration }),
        info: (title, message, duration) => showToast({ title, message, type: 'info', duration })
    };

    // Legacy support - replace old showToast functions
    window.showToast = function(message, type = 'success') {
        showToast({
            title: type === 'success' ? 'Success' : type === 'error' ? 'Error' : 'Info',
            message: message,
            type: type
        });
    };

})();
