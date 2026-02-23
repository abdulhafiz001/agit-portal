<!-- Faculty - My Courses -->
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-gray-900">My Courses</h2>
        <p class="text-sm text-gray-500 mt-1">Courses you have been assigned to teach</p>
    </div>

    <div id="courses-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="col-span-full text-center py-8 text-gray-400">Loading...</div>
    </div>
</div>

<script>
async function loadMyCourses() {
    const data = await API.get('/api/faculty/courses');
    const grid = document.getElementById('courses-grid');
    if (!data || !data.success) return;
    
    if (data.data.length === 0) {
        grid.innerHTML = '<div class="col-span-full text-center py-12 text-gray-400"><i class="fas fa-book text-4xl mb-3 block"></i>No courses assigned to you yet</div>';
        return;
    }
    
    grid.innerHTML = data.data.map(c => {
        const img = c.image ? `<img src="${APP_URL}/uploads/${c.image}" alt="" class="w-full h-36 object-cover rounded-t-xl">` : `<div class="w-full h-36 bg-emerald-100 rounded-t-xl flex items-center justify-center"><i class="fas fa-book text-4xl text-emerald-600"></i></div>`;
        const topicsHtml = c.topics && c.topics.length ? `<ul class="mt-2 space-y-1 text-xs text-gray-600">${c.topics.map(t => `<li><i class="fas fa-check text-emerald-500 mr-1"></i>${escapeHtml(t.topic_title)}</li>`).join('')}</ul>` : '';
        return `<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
            ${img}
            <div class="p-4">
                <h4 class="font-semibold text-gray-900">${escapeHtml(c.name)}</h4>
                <p class="text-xs text-gray-500 font-mono">${escapeHtml(c.code)}</p>
                ${c.duration ? `<p class="text-xs text-emerald-600 mt-1"><i class="fas fa-clock mr-1"></i>${escapeHtml(c.duration)}</p>` : ''}
                ${c.class_names ? `<p class="text-xs text-gray-500 mt-1"><i class="fas fa-school mr-1"></i>${escapeHtml(c.class_names)}</p>` : ''}
                ${topicsHtml}
            </div>
        </div>`;
    }).join('');
}

document.addEventListener('DOMContentLoaded', loadMyCourses);
</script>
