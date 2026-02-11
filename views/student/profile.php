<!-- Student Profile - Unique Design -->
<div class="max-w-4xl mx-auto">
    <!-- Profile Hero Banner -->
    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-2xl p-6 sm:p-8 mb-6 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 400 200"><circle cx="50" cy="50" r="80" fill="white"/><circle cx="350" cy="150" r="120" fill="white"/><circle cx="200" cy="30" r="60" fill="white"/></svg>
        </div>
        <div class="relative flex flex-col sm:flex-row items-center gap-5">
            <!-- Profile Picture -->
            <div class="relative group cursor-pointer" onclick="document.getElementById('pp-upload').click()">
                <div id="avatar-container" class="w-24 h-24 sm:w-28 sm:h-28 rounded-full border-4 border-white/30 overflow-hidden bg-white/20 flex items-center justify-center">
                    <img id="avatar-img" src="" class="w-full h-full object-cover hidden">
                    <span id="avatar-initials" class="text-3xl sm:text-4xl font-bold text-white">--</span>
                </div>
                <div class="absolute inset-0 rounded-full bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <i class="fas fa-camera text-white text-xl"></i>
                </div>
                <input type="file" id="pp-upload" accept="image/*" class="hidden" onchange="uploadPicture(this)">
            </div>
            <!-- Student Info -->
            <div class="text-center sm:text-left">
                <h2 id="hero-name" class="text-2xl sm:text-3xl font-bold text-white">Loading...</h2>
                <p id="hero-matric" class="text-white/70 text-sm mt-1 font-mono"></p>
                <div class="flex flex-wrap gap-2 mt-3 justify-center sm:justify-start">
                    <span id="hero-class" class="px-3 py-1 bg-white/20 text-white text-xs rounded-full backdrop-blur-sm"><i class="fas fa-school mr-1"></i>--</span>
                    <span id="hero-status" class="px-3 py-1 bg-green-400/30 text-green-100 text-xs rounded-full backdrop-blur-sm"><i class="fas fa-check-circle mr-1"></i>Active</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Cards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Personal Information Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900 flex items-center gap-2"><i class="fas fa-user text-blue-600"></i> Personal Information</h3>
            </div>
            <form id="profile-form" class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Full Name</label>
                    <input type="text" id="p-name" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Email Address</label>
                    <input type="email" id="p-email" disabled class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Phone</label>
                        <input type="text" id="p-phone" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Gender</label>
                        <input type="text" id="p-gender" disabled class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Address</label>
                    <input type="text" id="p-address" disabled class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500">
                </div>
                <button type="submit" id="save-profile-btn" class="w-full py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 text-sm font-medium transition-all">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </form>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Academic Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-50 to-teal-50 px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2"><i class="fas fa-graduation-cap text-emerald-600"></i> Academic Information</h3>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                        <span class="text-sm text-gray-500">Matric Number</span>
                        <span id="p-matric" class="text-sm font-mono font-semibold text-gray-900">--</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                        <span class="text-sm text-gray-500">Class</span>
                        <span id="p-class" class="text-sm font-semibold text-gray-900">--</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                        <span class="text-sm text-gray-500">Date of Birth</span>
                        <span id="p-dob" class="text-sm text-gray-900">--</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                        <span class="text-sm text-gray-500">Guardian</span>
                        <span id="p-guardian" class="text-sm text-gray-900">--</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-500">Enrolled Since</span>
                        <span id="p-joined" class="text-sm text-gray-900">--</span>
                    </div>
                </div>
            </div>

            <!-- Change Password Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2"><i class="fas fa-lock text-amber-600"></i> Change Password</h3>
                </div>
                <form id="password-form" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Current Password</label>
                        <input type="password" id="pw-current" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">New Password</label>
                        <input type="password" id="pw-new" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Confirm New Password</label>
                        <input type="password" id="pw-confirm" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 outline-none">
                    </div>
                    <button type="submit" id="change-pw-btn" class="w-full py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-lg hover:from-amber-600 hover:to-orange-600 text-sm font-medium transition-all">
                        <i class="fas fa-key mr-2"></i>Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
async function loadProfile() {
    const data = await API.get('/api/profile');
    if (!data || !data.success) return;
    const d = data.data;

    // Hero section
    document.getElementById('hero-name').textContent = d.name || 'Student';
    document.getElementById('hero-matric').textContent = d.matric_no || '';
    document.getElementById('hero-class').innerHTML = '<i class="fas fa-school mr-1"></i>' + (d.class_name || 'Not Assigned');
    document.getElementById('hero-status').innerHTML = '<i class="fas fa-check-circle mr-1"></i>' + (d.status || 'active');
    document.getElementById('avatar-initials').textContent = (d.name || '--').substring(0, 2).toUpperCase();

    if (d.profile_picture) {
        const img = document.getElementById('avatar-img');
        img.src = APP_URL + '/uploads/' + d.profile_picture;
        img.classList.remove('hidden');
        document.getElementById('avatar-initials').classList.add('hidden');
    }

    // Form fields
    document.getElementById('p-name').value = d.name || '';
    document.getElementById('p-email').value = d.email || '';
    document.getElementById('p-phone').value = d.phone || '';
    document.getElementById('p-gender').value = d.gender || 'N/A';
    document.getElementById('p-address').value = d.address || 'N/A';

    // Academic info
    document.getElementById('p-matric').textContent = d.matric_no || '--';
    document.getElementById('p-class').textContent = d.class_name || 'Not Assigned';
    document.getElementById('p-dob').textContent = d.date_of_birth || 'N/A';
    document.getElementById('p-guardian').textContent = d.guardian_name || 'N/A';
    document.getElementById('p-joined').textContent = d.created_at ? new Date(d.created_at).toLocaleDateString() : '--';
}

async function uploadPicture(input) {
    if (!input.files[0]) return;
    const fd = new FormData();
    fd.append('profile_picture', input.files[0]);
    const data = await API.upload('/api/profile/picture', fd);
    if (data && data.success) {
        Toast.success(data.message);
        const img = document.getElementById('avatar-img');
        img.src = APP_URL + '/uploads/' + data.path + '?t=' + Date.now();
        img.classList.remove('hidden');
        document.getElementById('avatar-initials').classList.add('hidden');
    } else if (data) Toast.error(data.message);
}

document.getElementById('profile-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('save-profile-btn');
    setLoading(btn, true);
    const data = await API.put('/api/profile', { name: document.getElementById('p-name').value, phone: document.getElementById('p-phone').value });
    setLoading(btn, false);
    if (data && data.success) Toast.success(data.message); else if (data) Toast.error(data.message);
});
document.getElementById('password-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('change-pw-btn');
    setLoading(btn, true);
    const data = await API.post('/api/profile/password', {
        current_password: document.getElementById('pw-current').value,
        new_password: document.getElementById('pw-new').value,
        confirm_password: document.getElementById('pw-confirm').value
    });
    setLoading(btn, false);
    if (data && data.success) { Toast.success(data.message); document.getElementById('password-form').reset(); }
    else if (data) Toast.error(data.message);
});
document.addEventListener('DOMContentLoaded', loadProfile);
</script>
