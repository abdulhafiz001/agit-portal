<!-- Faculty Schedule -->
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div><h2 class="text-xl font-bold text-gray-900">My Schedule</h2>
            <p class="text-sm text-gray-500 mt-1">View and manage your teaching schedule</p></div>
        <button onclick="showAddSchedule()" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium"><i class="fas fa-plus mr-2"></i>Add Schedule</button>
    </div>
    <div id="schedule-timetable" class="grid gap-4">
        <div class="text-center py-8 text-gray-400">Loading schedule...</div>
    </div>
</div>
<div id="sch-modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Add Schedule</h3>
            <button onclick="Modal.close('sch-modal')" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"><i class="fas fa-times"></i></button>
        </div>
        <form id="sch-form" class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Class</label><select id="sch-class" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm"></select></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Subject</label><select id="sch-subject" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm"></select></div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Day</label>
                    <select id="sch-day" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm">
                        <option value="monday">Monday</option><option value="tuesday">Tuesday</option><option value="wednesday">Wednesday</option><option value="thursday">Thursday</option><option value="friday">Friday</option><option value="saturday">Saturday</option>
                    </select></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Start</label><input type="time" id="sch-start" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">End</label><input type="time" id="sch-end" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm"></div>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Room</label><input type="text" id="sch-room" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm" placeholder="e.g. Hall A"></div>
            <button type="submit" class="w-full py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">Save</button>
        </form>
    </div>
</div>
<script>
const dayColors={monday:'blue',tuesday:'emerald',wednesday:'amber',thursday:'purple',friday:'pink',saturday:'red'};
const days=['monday','tuesday','wednesday','thursday','friday','saturday'];
async function loadSchedule() {
    const data = await API.get('/api/faculty/schedules');
    const container = document.getElementById('schedule-timetable');
    if(!data?.success||!data.data.length){container.innerHTML='<div class="text-center py-12 text-gray-400"><i class="fas fa-calendar-alt text-4xl mb-3 block"></i>No schedule set</div>';return;}
    const grouped={};
    data.data.forEach(s=>{if(!grouped[s.day_of_week])grouped[s.day_of_week]=[];grouped[s.day_of_week].push(s);});
    container.innerHTML = days.filter(d=>grouped[d]).map(d=>`
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-${dayColors[d]||'gray'}-50 px-5 py-3 border-b border-gray-100"><h3 class="font-semibold text-${dayColors[d]||'gray'}-700 capitalize"><i class="fas fa-calendar-day mr-2"></i>${d}</h3></div>
            <div class="divide-y divide-gray-50">${grouped[d].map(s=>`
                <div class="p-4 flex items-center gap-4"><div class="text-center"><div class="text-sm font-bold text-gray-900">${s.start_time?.substring(0,5)}</div><div class="text-xs text-gray-400">${s.end_time?.substring(0,5)}</div></div>
                <div class="flex-1"><div class="font-medium text-gray-900 text-sm">${escapeHtml(s.subject_name)} (${escapeHtml(s.subject_code)})</div><div class="text-xs text-gray-500">${escapeHtml(s.class_name)} ${s.room?'· Room: '+escapeHtml(s.room):''}</div></div></div>
            `).join('')}</div>
        </div>`).join('');
}
async function loadOptions() {
    const [cls, sub] = await Promise.all([API.get('/api/faculty/classes'), API.get('/api/faculty/scores/options')]);
    if(cls?.success) document.getElementById('sch-class').innerHTML='<option value="">Select</option>'+cls.data.map(c=>`<option value="${c.id}">${escapeHtml(c.name)}</option>`).join('');
    if(sub?.success) document.getElementById('sch-subject').innerHTML='<option value="">Select</option>'+sub.subjects.map(s=>`<option value="${s.id}">${escapeHtml(s.name)}</option>`).join('');
}
function showAddSchedule(){document.getElementById('sch-form').reset();Modal.open('sch-modal');}
document.getElementById('sch-form').addEventListener('submit',async(e)=>{
    e.preventDefault();
    const body={class_id:document.getElementById('sch-class').value,subject_id:document.getElementById('sch-subject').value,day_of_week:document.getElementById('sch-day').value,start_time:document.getElementById('sch-start').value,end_time:document.getElementById('sch-end').value,room:document.getElementById('sch-room').value};
    const data=await API.post('/api/faculty/schedules',body);
    if(data?.success){Toast.success(data.message);Modal.close('sch-modal');loadSchedule();}else if(data)Toast.error(data.message);
});
document.addEventListener('DOMContentLoaded',()=>{loadOptions();loadSchedule();});
</script>
