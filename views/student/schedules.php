<!-- Student Class Schedule -->
<div class="space-y-6">
    <div><h2 class="text-xl font-bold text-gray-900">Class Schedule</h2>
        <p class="text-sm text-gray-500 mt-1">Your weekly class timetable</p></div>
    <div id="schedule-timetable" class="grid gap-4">
        <div class="text-center py-8 text-gray-400">Loading schedule...</div>
    </div>
</div>
<script>
const dayColors={monday:'blue',tuesday:'emerald',wednesday:'amber',thursday:'purple',friday:'pink',saturday:'red'};
const days=['monday','tuesday','wednesday','thursday','friday','saturday'];
async function loadSchedule() {
    const data = await API.get('/api/student/schedules');
    const container = document.getElementById('schedule-timetable');
    if(!data?.success||!data.data.length){container.innerHTML='<div class="text-center py-12 text-gray-400"><i class="fas fa-calendar-alt text-4xl mb-3 block"></i>No class schedule available</div>';return;}
    const grouped={};
    data.data.forEach(s=>{if(!grouped[s.day_of_week])grouped[s.day_of_week]=[];grouped[s.day_of_week].push(s);});
    container.innerHTML = days.filter(d=>grouped[d]).map(d=>`
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-${dayColors[d]||'gray'}-50 px-5 py-3 border-b border-gray-100"><h3 class="font-semibold text-${dayColors[d]||'gray'}-700 capitalize"><i class="fas fa-calendar-day mr-2"></i>${d}</h3></div>
            <div class="divide-y divide-gray-50">${grouped[d].map(s=>`
                <div class="p-4 flex items-center gap-4"><div class="text-center min-w-[60px]"><div class="text-sm font-bold text-gray-900">${s.start_time?.substring(0,5)}</div><div class="text-xs text-gray-400">${s.end_time?.substring(0,5)}</div></div>
                <div class="flex-1"><div class="font-medium text-gray-900 text-sm">${escapeHtml(s.subject_name)} (${escapeHtml(s.subject_code)})</div><div class="text-xs text-gray-500">${escapeHtml(s.lecturer_name)} ${s.room?'· Room: '+escapeHtml(s.room):''}</div></div></div>
            `).join('')}</div>
        </div>`).join('');
}
document.addEventListener('DOMContentLoaded',loadSchedule);
</script>
