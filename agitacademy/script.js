// AGIT Academy JavaScript

// Warn if opened via file:// - fetch will be blocked
if (window.location.protocol === 'file:') {
    console.warn('AGIT Academy: Open this page via http://localhost/agit-portal/agitacademy/ for full functionality.');
}

// Use data-portal-api on <html>, or derive from current page path, or fallback
function getPortalApi() {
    var api = document.documentElement && document.documentElement.dataset && document.documentElement.dataset.portalApi;
    if (api) return api.replace(/\/$/, '');
    if (['localhost', '127.0.0.1'].includes(window.location.hostname)) {
        return 'http://localhost/agit-portal';
    }
    return 'https://www.portal.agitacademy.com/portal';
}
const PORTAL_API = getPortalApi();

function escapeHtml(s) {
    var d = document.createElement('div');
    d.textContent = s || '';
    return d.innerHTML;
}

document.addEventListener('DOMContentLoaded', function() {
    // --- CRITICAL: Attach form handler FIRST to prevent page reload ---
    var enrollmentForm = document.getElementById('enrollment-form');
    if (enrollmentForm) {
        enrollmentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var btn = document.getElementById('submit-btn');
            var msgEl = document.getElementById('form-message');
            if (msgEl) msgEl.classList.add('hidden');

            var formData = new FormData(enrollmentForm);
            var payload = {
                name: formData.get('name') || '',
                email: formData.get('email') || '',
                phone: formData.get('phone') || '',
                subject: formData.get('subject') || 'Enrollment Inquiry',
                message: formData.get('message') || ''
            };

            if (!payload.name || !payload.email || !payload.message) {
                showFormMessage(msgEl, 'Please fill in Name, Email, and Message.', 'error');
                return false;
            }

            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="animate-pulse">Sending...</span>';
            }

            fetch(PORTAL_API + '/api/contact', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success) {
                    showFormMessage(msgEl, data.message || 'Thank you! Your inquiry has been sent successfully. We will get back to you soon.', 'success');
                    enrollmentForm.reset();
                } else {
                    showFormMessage(msgEl, data.message || 'Something went wrong. Please try again.', 'error');
                }
            })
            .catch(function(err) {
                showFormMessage(msgEl, 'Unable to send. Please check your connection and try again.', 'error');
            })
            .finally(function() {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = 'Submit Inquiry <i data-lucide="send" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>';
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                }
                if (msgEl) msgEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            });
            return false;
        });
    }

    function showFormMessage(el, text, type) {
        if (!el) return;
        var isSuccess = type === 'success';
        var icon = isSuccess ? 'check-circle' : 'alert-circle';
        var bgClass = isSuccess ? 'bg-green-500/20 text-green-400 border-green-500/30' : 'bg-red-500/20 text-red-400 border-red-500/30';
        el.innerHTML = '<i data-lucide="' + icon + '" class="w-5 h-5 flex-shrink-0 mt-0.5"></i><span>' + escapeHtml(text) + '</span>';
        el.className = 'rounded-xl p-4 text-sm flex items-start gap-3 border ' + bgClass;
        el.classList.remove('hidden');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    // Initialize Lucide Icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Load programs from portal API
    loadPrograms();

    // Navbar scroll effect (with null checks)
    var navbar = document.getElementById('navbar');
    var scrollThreshold = 50;

    function handleScroll() {
        if (!navbar) return;
        if (window.scrollY > scrollThreshold) {
            navbar.classList.add('navbar-scrolled');
        } else {
            navbar.classList.remove('navbar-scrolled');
        }
        var scrollTopBtn = document.getElementById('scroll-top');
        if (scrollTopBtn) {
            if (window.scrollY > 300) {
                scrollTopBtn.classList.add('visible');
            } else {
                scrollTopBtn.classList.remove('visible');
            }
        }
    }

    window.addEventListener('scroll', handleScroll);
    handleScroll();

    // Mobile menu toggle (with null checks)
    var mobileMenuBtn = document.getElementById('mobile-menu-btn');
    var mobileMenu = document.getElementById('mobile-menu');
    var menuIcon = document.getElementById('menu-icon');
    var isMenuOpen = false;

    function closeMenu() {
        isMenuOpen = false;
        if (mobileMenu) {
            mobileMenu.classList.add('hidden');
            mobileMenu.classList.remove('show');
        }
        if (menuIcon) menuIcon.setAttribute('data-lucide', 'menu');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function openMenu() {
        isMenuOpen = true;
        if (mobileMenu) {
            mobileMenu.classList.remove('hidden');
            mobileMenu.classList.add('show');
        }
        if (menuIcon) menuIcon.setAttribute('data-lucide', 'x');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (isMenuOpen) closeMenu();
            else openMenu();
        });
    }

    if (mobileMenu) {
        var mobileMenuLinks = mobileMenu.querySelectorAll('a');
        mobileMenuLinks.forEach(function(link) {
            link.addEventListener('click', closeMenu);
        });
    }

    document.addEventListener('click', function(e) {
        if (isMenuOpen && mobileMenu && mobileMenuBtn && !mobileMenu.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
            closeMenu();
        }
    });

    // Scroll to top
    var scrollTopBtn = document.getElementById('scroll-top');
    if (scrollTopBtn) {
        scrollTopBtn.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // Load programs from portal API
    function loadPrograms() {
        var grid = document.getElementById('programs-grid');
        var loading = document.getElementById('programs-loading');
        var empty = document.getElementById('programs-empty');
        var programSelect = document.querySelector('#enrollment-form select[name="subject"]');
        if (!grid || !loading) return;

        var controller = new AbortController();
        var timeoutId = setTimeout(function() { controller.abort(); }, 15000);

        fetch(PORTAL_API + '/api/landing/courses', { signal: controller.signal })
            .then(function(res) {
                clearTimeout(timeoutId);
                if (!res.ok) throw new Error('API returned ' + res.status);
                return res.json();
            })
            .then(function(data) {
                loading.classList.add('hidden');
                var courses = (data && data.success && data.data) ? data.data : [];

                if (programSelect && courses.length > 0) {
                    programSelect.innerHTML = '';
                    var opt0 = document.createElement('option');
                    opt0.value = '';
                    opt0.textContent = 'Select Program';
                    opt0.className = 'bg-dark-900';
                    programSelect.appendChild(opt0);
                    courses.forEach(function(c) {
                        var opt = document.createElement('option');
                        opt.value = c.name || '';
                        opt.textContent = c.name || '';
                        opt.className = 'bg-dark-900';
                        programSelect.appendChild(opt);
                    });
                }

                if (courses.length === 0) {
                    if (empty) empty.classList.remove('hidden');
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                    return;
                }

                var defaultImg = 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=600&q=80';
                courses.forEach(function(c) {
                    var imgUrl = c.image_url || defaultImg;
                    var duration = c.duration || '';
                    var fullDesc = c.description || '';
                    var desc = fullDesc.length > 120 ? fullDesc.substring(0, 120) + '...' : fullDesc;
                    var topics = (c.topics || []);
                    var topicsHtml = topics.length ? topics.slice(0, 6).map(function(t) {
                        return '<li class="flex items-start gap-2 text-sm text-gray-400"><i data-lucide="check" class="w-4 h-4 text-academy-400 mt-0.5 flex-shrink-0"></i>' + escapeHtml(t) + '</li>';
                    }).join('') + (topics.length > 6 ? '<li class="text-xs text-gray-500">+ ' + (topics.length - 6) + ' more topics</li>' : '') : '<li class="text-sm text-gray-500">Topics will be added by admin.</li>';
                    var card = document.createElement('div');
                    card.className = 'glass rounded-2xl overflow-hidden card-hover group';
                    card.innerHTML = '<div class="relative h-48 overflow-hidden">' +
                        '<img src="' + escapeHtml(imgUrl) + '" alt="' + escapeHtml(c.name) + '" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy">' +
                        '<div class="absolute inset-0 bg-gradient-to-t from-dark-900 via-dark-900/50 to-transparent"></div>' +
                        (duration ? '<div class="absolute bottom-4 left-4 right-4 flex items-center gap-2 text-sm text-white/80"><i data-lucide="clock" class="w-4 h-4"></i>' + escapeHtml(duration) + '</div>' : '') +
                        '</div>' +
                        '<div class="p-6">' +
                        '<div class="flex items-center gap-3 mb-4">' +
                        '<div class="w-12 h-12 rounded-xl bg-gradient-to-br from-academy-400 to-academy-600 flex items-center justify-center flex-shrink-0"><i data-lucide="book-open" class="w-6 h-6 text-white"></i></div>' +
                        '<div class="min-w-0"><h3 class="text-xl font-display font-bold text-white">' + escapeHtml(c.name) + '</h3><p class="text-sm text-gray-400 line-clamp-2">' + escapeHtml(desc) + '</p></div>' +
                        '</div>' +
                        '<div class="section-divider my-4"></div>' +
                        '<h4 class="text-xs font-semibold text-academy-400 uppercase tracking-wider mb-3">What You\'ll Learn</h4>' +
                        '<ul class="grid grid-cols-1 gap-2 mb-6">' + topicsHtml + '</ul>' +
                        '<a href="#contact" class="inline-flex items-center gap-2 text-academy-400 hover:text-academy-300 font-semibold text-sm transition-colors">Learn More <i data-lucide="arrow-right" class="w-4 h-4"></i></a>' +
                        '</div>';
                    grid.appendChild(card);
                });
                if (typeof lucide !== 'undefined') lucide.createIcons();
            })
            .catch(function(err) {
                clearTimeout(timeoutId);
                loading.classList.add('hidden');
                var errMsg = 'Unable to load programs. ';
                if (err.name === 'AbortError') errMsg += 'Request timed out.';
                else if (err.message) errMsg += err.message;
                else errMsg += 'Please try again later.';
                loading.innerHTML = '<p class="text-gray-500">' + escapeHtml(errMsg) + '</p>';
                loading.classList.remove('hidden');
                if (empty) empty.classList.add('hidden');
            });
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            var href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                var target = document.querySelector(href);
                if (target && navbar) {
                    var navbarHeight = navbar.offsetHeight;
                    var targetPosition = target.getBoundingClientRect().top + window.pageYOffset - navbarHeight;
                    window.scrollTo({ top: targetPosition, behavior: 'smooth' });
                }
            }
        });
    });

    // Intersection Observer for scroll animations
    var observerOptions = { threshold: 0.1, rootMargin: '0px 0px -100px 0px' };
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('section > div').forEach(function(el) {
        el.style.opacity = '0';
        observer.observe(el);
    });

    document.querySelectorAll('.card-hover').forEach(function(card, index) {
        card.style.animationDelay = (index * 0.1) + 's';
    });
});
