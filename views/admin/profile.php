<!-- Admin Profile -->
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-6 sm:p-8 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10"><svg class="w-full h-full" viewBox="0 0 400 200"><circle cx="50" cy="50" r="80" fill="white"/><circle cx="350" cy="150" r="120" fill="white"/></svg></div>
        <div class="relative flex flex-col sm:flex-row items-center gap-5">
            <div class="relative group cursor-pointer" onclick="document.getElementById('pp-upload').click()">
                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-full border-4 border-white/30 overflow-hidden bg-white/20 flex items-center justify-center">
                    <img id="avatar-img" src="" class="w-full h-full object-cover hidden">
                    <span id="avatar-initials" class="text-3xl sm:text-4xl font-bold text-white">--</span>
                </div>
                <div class="absolute inset-0 rounded-full bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"><i class="fas fa-camera text-white text-xl"></i></div>
                <input type="file" id="pp-upload" accept="image/*" class="hidden" onchange="uploadPicture(this)">
            </div>
            <div class="text-center sm:text-left">
                <h2 id="hero-name" class="text-2xl sm:text-3xl font-bold text-white">Loading...</h2>
                <p id="hero-email" class="text-white/70 text-sm mt-1"></p>
                <span class="inline-block mt-2 px-3 py-1 bg-white/20 text-white text-xs rounded-full"><i class="fas fa-shield-alt mr-1"></i>Administrator</span>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b"><h3 class="font-semibold text-gray-900 flex items-center gap-2"><i class="fas fa-user text-blue-600"></i> Account Information</h3></div>
            <form id="profile-form" class="p-6 space-y-4">
                <div><label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Full Name</label><input type="text" id="p-name" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm"></div>
                <div><label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Email</label><input type="email" id="p-email" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"></div>
                <div id="p-phone-wrap" class="hidden"><label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Phone</label><input type="text" id="p-phone" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm"></div>
                <button type="submit" id="save-profile-btn" class="w-full py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg text-sm font-medium"><i class="fas fa-save mr-2"></i>Save Changes</button>
            </form>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b"><h3 class="font-semibold text-gray-900 flex items-center gap-2"><i class="fas fa-lock text-amber-600"></i> Change Password</h3></div>
            <form id="password-form" class="p-6 space-y-4">
                <div><label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Current Password</label><input type="password" id="pw-current" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm"></div>
                <div><label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">New Password</label><input type="password" id="pw-new" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm"></div>
                <div><label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Confirm Password</label><input type="password" id="pw-confirm" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm"></div>
                <button type="submit" id="change-pw-btn" class="w-full py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-lg text-sm font-medium"><i class="fas fa-key mr-2"></i>Update Password</button>
            </form>
        </div>
    </div>
</div>
<script>
async function loadProfile() {
    const data = await API.get('/api/profile');
    if (!data || !data.success) return;
    const d = data.data;
    document.getElementById('hero-name').textContent = d.name || 'Admin';
    document.getElementById('hero-email').textContent = d.email || '';
    document.getElementById('avatar-initials').textContent = (d.name || '--').substring(0, 2).toUpperCase();
    if (d.profile_picture) {
        const img = document.getElementById('avatar-img');
        img.src = APP_URL + '/uploads/' + d.profile_picture;
        img.classList.remove('hidden');
        document.getElementById('avatar-initials').classList.add('hidden');
    }
    document.getElementById('p-name').value = d.name || '';
    document.getElementById('p-email').value = d.email || '';
    const phoneWrap = document.getElementById('p-phone-wrap');
    const phoneInput = document.getElementById('p-phone');
    if (phoneWrap && phoneInput) {
        if (d.phone !== undefined && d.phone !== null) {
            phoneWrap.classList.remove('hidden');
            phoneInput.value = d.phone || '';
        }
    }
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
    const btn = document.getElementById('save-profile-btn'); setLoading(btn, true);
    const payload = { name: document.getElementById('p-name').value, email: document.getElementById('p-email').value };
    const phoneEl = document.getElementById('p-phone');
    if (phoneEl && document.getElementById('p-phone-wrap') && !document.getElementById('p-phone-wrap').classList.contains('hidden')) {
        payload.phone = phoneEl.value;
    }
    const data = await API.put('/api/profile', payload);
    setLoading(btn, false);
    if (data && data.success) { Toast.success(data.message); document.getElementById('hero-email').textContent = document.getElementById('p-email').value; }
    else if (data) Toast.error(data.message);
});
document.getElementById('password-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('change-pw-btn'); setLoading(btn, true);
    const data = await API.post('/api/profile/password', { current_password: document.getElementById('pw-current').value, new_password: document.getElementById('pw-new').value, confirm_password: document.getElementById('pw-confirm').value });
    setLoading(btn, false);
    if (data && data.success) { Toast.success(data.message); document.getElementById('password-form').reset(); } else if (data) Toast.error(data.message);
});
document.addEventListener('DOMContentLoaded', loadProfile);
</script>
