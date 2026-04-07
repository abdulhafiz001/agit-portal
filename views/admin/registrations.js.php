<script>
(function() {
    async function loadStats() {
        try {
            const d = await API.get('/api/admin/registrations/stats');
            if (!d || !d.success) {
                document.getElementById('stat-pending').textContent = '0';
                document.getElementById('stat-approved').textContent = '0';
                document.getElementById('stat-rejected').textContent = '0';
                document.getElementById('stat-total').textContent = '0';
                return;
            }
            const data = d.data;
            document.getElementById('stat-pending').textContent = data.pending ?? 0;
            document.getElementById('stat-approved').textContent = data.approved ?? 0;
            document.getElementById('stat-rejected').textContent = data.rejected ?? 0;
            document.getElementById('stat-total').textContent = (data.pending ?? 0) + (data.approved ?? 0) + (data.rejected ?? 0);

            const byClass = data.by_class || [];
            if (byClass.length && typeof Chart !== 'undefined') {
                const ctx = document.getElementById('chart-by-class').getContext('2d');
                if (window.registrationsChart) window.registrationsChart.destroy();
                window.registrationsChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: byClass.map(c => c.class_name || 'Unassigned'),
                        datasets: [
                            { label: 'Pending', data: byClass.map(c => c.pending || 0), backgroundColor: '#f59e0b' },
                            { label: 'Approved', data: byClass.map(c => c.approved || 0), backgroundColor: '#10b981' },
                            { label: 'Rejected', data: byClass.map(c => c.rejected || 0), backgroundColor: '#ef4444' }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'top' } },
                        scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } }
                    }
                });
            }
        } catch (e) {
            console.error('loadStats error:', e);
            document.getElementById('stat-pending').textContent = '-';
            document.getElementById('stat-approved').textContent = '-';
            document.getElementById('stat-rejected').textContent = '-';
            document.getElementById('stat-total').textContent = '-';
        }
    }

    let classesCache = null;
    let classCoursesCache = {};

    async function loadClasses() {
        if (classesCache) return classesCache;
        const d = await API.get('/api/admin/classes');
        if (d && d.success) classesCache = d.data || [];
        else classesCache = [];
        return classesCache;
    }

    async function loadPending() {
        const el = document.getElementById('pending-list');
        try {
            const d = await API.get('/api/admin/registrations');
            if (!d || !d.success) {
                const msg = (d && d.message) ? d.message : 'Session may have expired. Please refresh the page and log in again.';
                el.innerHTML = '<div class="text-center py-12 text-amber-600"><i class="fas fa-exclamation-triangle text-4xl mb-3"></i><p>Could not load pending registrations.</p><p class="text-sm mt-2">' + escapeHtml(msg) + '</p><button onclick="window.loadPending()" class="mt-4 px-4 py-2 bg-amber-100 text-amber-700 rounded-lg text-sm hover:bg-amber-200">Retry</button></div>';
                return;
            }
            const list = d.data || [];
            if (list.length === 0) {
                el.innerHTML = '<div class="text-center py-12 text-gray-500"><i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i><p>No pending registrations</p></div>';
                return;
            }
            el.innerHTML = '<div class="space-y-4">' + list.map(s => {
                const verified = Number(s.email_verified) === 1;
                const badgeClass = verified
                    ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                    : 'bg-amber-50 text-amber-700 border border-amber-200';
                const badgeIcon = verified ? 'fa-circle-check' : 'fa-envelope-open-text';
                const badgeText = verified ? 'Email verified' : 'Email unverified';
                const initials = (s.name || '?').split(' ').map(part => part.charAt(0)).join('').slice(0, 2).toUpperCase();
                return `
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-shadow">
                    <div class="p-5">
                        <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-5">
                            <div class="flex gap-4 min-w-0">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-sm shrink-0">
                                    ${escapeHtml(initials)}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="text-base font-semibold text-gray-900">${escapeHtml(s.name)}</h4>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium ${badgeClass}">
                                            <i class="fas ${badgeIcon}"></i>
                                            ${badgeText}
                                        </span>
                                    </div>
                                    <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                        <div class="flex items-center gap-2 text-gray-600 min-w-0">
                                            <i class="fas fa-envelope text-gray-400 w-4"></i>
                                            <span class="truncate">${escapeHtml(s.email)}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-gray-600">
                                            <i class="fas fa-phone text-gray-400 w-4"></i>
                                            <span>${escapeHtml(s.phone || 'No phone provided')}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-gray-600">
                                            <i class="fas fa-layer-group text-gray-400 w-4"></i>
                                            <span>${escapeHtml(s.class_name || 'Class will be assigned on approval')}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-gray-600">
                                            <i class="fas fa-calendar-alt text-gray-400 w-4"></i>
                                            <span>Registered ${formatDate(s.created_at)}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row xl:flex-col gap-2 xl:min-w-[180px]">
                                <button onclick="window.showApproveModal(${s.id}, '${escapeHtml(s.name).replace(/'/g, "\\'")}', '${escapeHtml(s.email).replace(/'/g, "\\'")}')" class="px-4 py-2.5 bg-green-600 text-white rounded-xl text-sm font-medium hover:bg-green-700">
                                    <i class="fas fa-check mr-1"></i>Approve
                                </button>
                                <button onclick="window.showDeclineModal(${s.id})" class="px-4 py-2.5 bg-white text-red-600 border border-red-200 rounded-xl text-sm font-medium hover:bg-red-50">
                                    <i class="fas fa-times mr-1"></i>Decline
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
            }).join('') + '</div>';
        } catch (e) {
            console.error('loadPending error:', e);
            el.innerHTML = '<div class="text-center py-12 text-red-600"><i class="fas fa-exclamation-circle text-4xl mb-3"></i><p>Failed to load pending registrations.</p><button onclick="window.loadPending()" class="mt-4 px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm hover:bg-red-200">Retry</button></div>';
        }
    }

    function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
    function formatDate(d) { if (!d) return ''; const x = new Date(d); return x.toLocaleDateString() + ' ' + x.toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'}); }

    async function showApproveModal(id, name, email) {
        document.getElementById('approve-id').value = id;
        document.getElementById('approve-student-name').textContent = name;
        document.getElementById('approve-student-email').textContent = email;
        document.getElementById('approve-courses-preview').classList.add('hidden');

        const sel = document.getElementById('approve-class');
        sel.innerHTML = '<option value="">Select a class...</option>';
        const classes = await loadClasses();
        classes.forEach(c => {
            if (c.status === 'active') {
                sel.innerHTML += `<option value="${c.id}">${escapeHtml(c.name)}</option>`;
            }
        });
        Modal.open('approve-modal');
    }

    async function loadCoursesForClass(classId) {
        const preview = document.getElementById('approve-courses-preview');
        const list = document.getElementById('approve-courses-list');
        if (!classId) { preview.classList.add('hidden'); return; }

        if (classCoursesCache[classId]) {
            renderCourses(classCoursesCache[classId]);
            return;
        }
        list.innerHTML = '<span class="text-xs text-gray-400">Loading courses...</span>';
        preview.classList.remove('hidden');
        const d = await API.get('/api/admin/classes/' + classId);
        if (d && d.success && d.data && d.data.subjects) {
            classCoursesCache[classId] = d.data.subjects;
            renderCourses(d.data.subjects);
        } else {
            list.innerHTML = '<span class="text-xs text-gray-400">No courses found</span>';
        }
    }

    function renderCourses(subjects) {
        const list = document.getElementById('approve-courses-list');
        const preview = document.getElementById('approve-courses-preview');
        if (!subjects || subjects.length === 0) {
            list.innerHTML = '<span class="text-xs text-gray-400">No courses assigned to this class yet</span>';
        } else {
            list.innerHTML = subjects.map(s =>
                `<span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-50 text-green-700 border border-green-200 rounded-lg text-xs font-medium"><i class="fas fa-book text-[10px]"></i>${escapeHtml(s.name || s.subject_name)}</span>`
            ).join('');
        }
        preview.classList.remove('hidden');
    }

    document.getElementById('approve-class').addEventListener('change', function() {
        loadCoursesForClass(this.value);
    });

    document.getElementById('approve-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('approve-id').value;
        const classId = document.getElementById('approve-class').value;
        if (!classId) { Toast.error('Please select a class.'); return; }
        const btn = document.getElementById('approve-submit-btn');
        setLoading(btn, true);
        const d = await API.post('/api/admin/registrations/' + id + '/approve', { class_id: classId });
        setLoading(btn, false);
        if (d && d.success) { Toast.success(d.message); Modal.close('approve-modal'); loadStats(); loadPending(); }
        else if (d) Toast.error(d.message);
    });

    function showDeclineModal(id) {
        document.getElementById('decline-id').value = id;
        document.getElementById('decline-reason').value = '';
        Modal.open('decline-modal');
    }

    window.loadPending = loadPending;
    window.showApproveModal = showApproveModal;
    window.showDeclineModal = showDeclineModal;

    document.getElementById('decline-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('decline-id').value;
        const reason = document.getElementById('decline-reason').value.trim();
        if (reason.length < 10) { Toast.error('Reason must be at least 10 characters.'); return; }
        const d = await API.post('/api/admin/registrations/' + id + '/decline', { reason });
        if (d && d.success) { Toast.success(d.message); Modal.close('decline-modal'); loadStats(); loadPending(); }
        else if (d) Toast.error(d.message);
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => { loadStats(); loadPending(); });
    } else {
        loadStats();
        loadPending();
    }
})();
</script>
