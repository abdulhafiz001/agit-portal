<!-- Student - My Courses -->
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-gray-900">My Courses</h2>
        <p class="text-sm text-gray-500 mt-1" id="class-label">Loading...</p>
    </div>

    <div id="courses-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="col-span-full text-center py-8 text-gray-400">Loading courses...</div>
    </div>
</div>

<script>
async function loadMyCourses() {
    const data = await API.get('/api/student/courses');
    if (!data || !data.success) return;
    
    document.getElementById('class-label').textContent = `Class: ${data.class_name} - ${data.data.length} courses`;
    
    const grid = document.getElementById('courses-grid');
    if (data.data.length === 0) {
        grid.innerHTML = `
            <div class="col-span-full text-center py-12">
                <i class="fas fa-book-open text-gray-300 text-5xl mb-4 block"></i>
                <h3 class="text-lg font-semibold text-gray-500 mb-1">No Courses Yet</h3>
                <p class="text-gray-400 text-sm">Courses will appear here once your class has subjects assigned.</p>
            </div>
        `;
        return;
    }
    
    const colors = ['blue', 'emerald', 'amber', 'purple', 'rose', 'cyan', 'indigo', 'orange'];
    
    grid.innerHTML = data.data.map((s, i) => {
        const color = colors[i % colors.length];
        const img = s.image ? `<img src="${APP_URL}/uploads/${s.image}" class="w-full h-36 object-cover" alt="">` : `<div class="h-36 bg-${color}-100 flex items-center justify-center"><i class="fas fa-book text-4xl text-${color}-600"></i></div>`;
        const topicsHtml = s.topics && s.topics.length ? `<ul class="mt-2 space-y-0.5 text-xs text-gray-600">${s.topics.map(t => `<li><i class="fas fa-check text-${color}-500 mr-1 text-xs"></i>${escapeHtml(t)}</li>`).join('')}</ul>` : '';
        return `
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
            ${img}
            <div class="p-5">
                <div class="flex items-start justify-between mb-2">
                    <h4 class="font-semibold text-gray-900 text-sm">${escapeHtml(s.name)}</h4>
                    <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded">${escapeHtml(s.code)}</span>
                </div>
                ${s.duration ? `<p class="text-xs text-blue-600 mb-1"><i class="fas fa-clock mr-1"></i>${escapeHtml(s.duration)}</p>` : ''}
                ${s.description ? `<p class="text-xs text-gray-500 mb-2 line-clamp-2">${escapeHtml(s.description)}</p>` : ''}
                ${topicsHtml}
                ${s.lecturer_names ? `
                    <div class="flex items-center gap-2 text-xs text-gray-400 mt-3 pt-3 border-t border-gray-50">
                        <i class="fas fa-user"></i>
                        <span>${escapeHtml(s.lecturer_names)}</span>
                    </div>
                ` : ''}
            </div>
        </div>`;
    }).join('');
}

document.addEventListener('DOMContentLoaded', loadMyCourses);
</script>
