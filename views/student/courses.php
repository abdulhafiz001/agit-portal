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
    
    document.getElementById('class-label').textContent = `Class: ${data.class_name} - ${data.data.length} subjects`;
    
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
        return `
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
            <div class="h-2 bg-${color}-500"></div>
            <div class="p-5">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 bg-${color}-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-book text-${color}-600"></i>
                    </div>
                    <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">${escapeHtml(s.code)}</span>
                </div>
                <h4 class="font-semibold text-gray-900 text-sm mb-2">${escapeHtml(s.name)}</h4>
                ${s.description ? `<p class="text-xs text-gray-500 mb-3 line-clamp-2">${escapeHtml(s.description)}</p>` : ''}
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
