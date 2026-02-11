/**
 * AGIT Academy Management System - Global JavaScript
 */

const APP_URL = '/agit-portal';

// ============================================================
// Toast Notifications (Toastify style)
// ============================================================
const Toast = {
    container: null,

    init() {
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.id = 'toast-container';
            this.container.className = 'fixed top-4 right-4 z-[9999] flex flex-col gap-3 max-w-sm';
            document.body.appendChild(this.container);
        }
    },

    show(message, type = 'success', duration = 4000) {
        this.init();
        const toast = document.createElement('div');
        const icons = {
            success: '<i class="fas fa-check-circle"></i>',
            error: '<i class="fas fa-times-circle"></i>',
            warning: '<i class="fas fa-exclamation-triangle"></i>',
            info: '<i class="fas fa-info-circle"></i>'
        };
        const colors = {
            success: 'bg-emerald-500',
            error: 'bg-red-500',
            warning: 'bg-amber-500',
            info: 'bg-blue-500'
        };

        toast.className = `${colors[type]} text-white px-4 py-3 rounded-lg shadow-lg flex items-center gap-3 transform translate-x-full opacity-0 transition-all duration-300 cursor-pointer`;
        toast.innerHTML = `
            <span class="text-lg">${icons[type]}</span>
            <span class="text-sm font-medium flex-1">${message}</span>
            <button class="text-white/80 hover:text-white ml-2" onclick="this.parentElement.remove()">
                <i class="fas fa-times text-xs"></i>
            </button>
        `;

        this.container.appendChild(toast);

        // Animate in
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
        });

        // Auto remove
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, duration);

        toast.addEventListener('click', () => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        });
    },

    success(msg) { this.show(msg, 'success'); },
    error(msg) { this.show(msg, 'error', 5000); },
    warning(msg) { this.show(msg, 'warning'); },
    info(msg) { this.show(msg, 'info'); }
};

// ============================================================
// AJAX Helper
// ============================================================
const API = {
    async request(url, options = {}) {
        const defaults = {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        };

        if (options.body && !(options.body instanceof FormData)) {
            defaults.headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify(options.body);
        }

        const config = {
            ...defaults,
            ...options,
            headers: { ...defaults.headers, ...options.headers }
        };

        // Remove Content-Type for FormData (browser sets it with boundary)
        if (options.body instanceof FormData) {
            delete config.headers['Content-Type'];
        }

        try {
            const response = await fetch(APP_URL + url, config);
            const data = await response.json();
            
            if (response.status === 401) {
                Toast.error('Session expired. Redirecting to login...');
                setTimeout(() => window.location.href = APP_URL + '/', 2000);
                return null;
            }

            return data;
        } catch (error) {
            console.error('API Error:', error);
            Toast.error('Network error. Please try again.');
            return null;
        }
    },

    get(url) {
        return this.request(url, { method: 'GET' });
    },

    post(url, body) {
        return this.request(url, { method: 'POST', body });
    },

    put(url, body) {
        return this.request(url, { method: 'PUT', body });
    },

    delete(url) {
        return this.request(url, { method: 'DELETE' });
    },

    upload(url, formData) {
        return this.request(url, { method: 'POST', body: formData });
    }
};

// ============================================================
// Modal Helper
// ============================================================
const Modal = {
    open(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    },

    close(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }
    }
};

// Close modal on overlay click
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.add('hidden');
        e.target.classList.remove('flex');
        document.body.style.overflow = '';
    }
});

// ============================================================
// Sidebar Toggle
// ============================================================
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    
    if (sidebar) {
        sidebar.classList.toggle('-translate-x-full');
        sidebar.classList.toggle('lg:translate-x-0');
    }
}

function toggleSubmenu(id) {
    const submenu = document.getElementById(id);
    const arrow = document.getElementById(id + '-arrow');
    
    if (submenu) {
        submenu.classList.toggle('open');
        if (arrow) {
            arrow.classList.toggle('rotate-90');
        }
    }
}

// ============================================================
// Utility Functions
// ============================================================
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateStr) {
    if (!dateStr) return 'N/A';
    const d = new Date(dateStr);
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

// Confirm dialog
function confirmAction(message) {
    return new Promise((resolve) => {
        const overlay = document.createElement('div');
        overlay.className = 'fixed inset-0 z-[9999] flex items-center justify-center modal-overlay';
        overlay.innerHTML = `
            <div class="bg-white rounded-xl shadow-2xl p-6 max-w-sm mx-4 transform scale-95 transition-transform duration-200">
                <div class="text-center">
                    <div class="mx-auto w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mb-4">
                        <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Confirm Action</h3>
                    <p class="text-gray-500 text-sm mb-6">${message}</p>
                    <div class="flex gap-3 justify-center">
                        <button id="confirm-cancel" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
                        <button id="confirm-ok" class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600">Confirm</button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);
        requestAnimationFrame(() => overlay.querySelector('div > div').classList.remove('scale-95'));

        overlay.querySelector('#confirm-ok').addEventListener('click', () => { overlay.remove(); resolve(true); });
        overlay.querySelector('#confirm-cancel').addEventListener('click', () => { overlay.remove(); resolve(false); });
        overlay.addEventListener('click', (e) => { if (e.target === overlay) { overlay.remove(); resolve(false); } });
    });
}

// Loading state for buttons
function setLoading(btn, loading) {
    if (loading) {
        btn.disabled = true;
        btn.dataset.originalText = btn.innerHTML;
        btn.innerHTML = '<div class="spinner mx-auto"></div>';
    } else {
        btn.disabled = false;
        btn.innerHTML = btn.dataset.originalText || btn.innerHTML;
    }
}

// Initialize on DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
    // Auto-open active submenu
    const activeLink = document.querySelector('.sidebar-link.active');
    if (activeLink) {
        const submenu = activeLink.closest('.sidebar-submenu');
        if (submenu) {
            submenu.classList.add('open');
            const arrow = document.getElementById(submenu.id + '-arrow');
            if (arrow) arrow.classList.add('rotate-90');
        }
    }
});
