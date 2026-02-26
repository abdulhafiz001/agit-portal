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
            el.innerHTML = '<div class="space-y-4">' + list.map(s => `
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 bg-gray-50 rounded-xl">
                    <div>
                        <div class="font-semibold text-gray-900">${escapeHtml(s.name)}</div>
                        <div class="text-sm text-gray-500">${escapeHtml(s.email)}</div>
                        <div class="text-sm text-gray-500">${escapeHtml(s.class_name || '')} • ${escapeHtml(s.phone || '')}</div>
                        <div class="text-xs text-gray-400 mt-1">Registered ${formatDate(s.created_at)}</div>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="window.approveStudent(${s.id})" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700"><i class="fas fa-check mr-1"></i>Accept</button>
                        <button onclick="window.showDeclineModal(${s.id})" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700"><i class="fas fa-times mr-1"></i>Decline</button>
                    </div>
                </div>
            `).join('') + '</div>';
        } catch (e) {
            console.error('loadPending error:', e);
            el.innerHTML = '<div class="text-center py-12 text-red-600"><i class="fas fa-exclamation-circle text-4xl mb-3"></i><p>Failed to load pending registrations.</p><button onclick="window.loadPending()" class="mt-4 px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm hover:bg-red-200">Retry</button></div>';
        }
    }

    function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
    function formatDate(d) { if (!d) return ''; const x = new Date(d); return x.toLocaleDateString() + ' ' + x.toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'}); }

    async function approveStudent(id) {
        const yes = await confirmAction('Approve this student? A matric number will be generated and they will be notified.');
        if (!yes) return;
        const d = await API.post('/api/admin/registrations/' + id + '/approve', {});
        if (d && d.success) { Toast.success(d.message); loadStats(); loadPending(); }
        else if (d) Toast.error(d.message);
    }

    function showDeclineModal(id) {
        document.getElementById('decline-id').value = id;
        document.getElementById('decline-reason').value = '';
        Modal.open('decline-modal');
    }

    window.loadPending = loadPending;
    window.approveStudent = approveStudent;
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
