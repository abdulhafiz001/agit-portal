<!-- Student Results Page -->
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">My Results</h2>
            <p class="text-sm text-gray-500 mt-1">View your academic performance and exam results</p>
        </div>
        <button onclick="downloadResult()" id="download-btn" class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium hidden w-full sm:w-auto"><i class="fas fa-download mr-2"></i>Download Result</button>
    </div>

    <!-- Summary Cards -->
    <div id="result-summary" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4"></div>

    <!-- Performance Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-5 overflow-hidden">
        <h4 class="text-sm font-semibold text-gray-900 mb-4">Subject Performance</h4>
        <div class="relative w-full chart-container">
            <canvas id="perfChart"></canvas>
        </div>
    </div>

    <!-- Subject Scores -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100"><h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-book-open mr-2 text-blue-600"></i>Subject Scores</h3></div>
        <div class="overflow-x-auto overflow-y-auto max-h-[calc(100vh-280px)] sm:max-h-[65vh] table-responsive">
            <table class="w-full text-sm" style="min-width: 860px;">
                <thead class="bg-gray-50"><tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500">Subject</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500">Class</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500">CA</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500">Exam</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500">Total</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500">Grade</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500">Remark</th>
                </tr></thead>
                <tbody id="scores-body"><tr><td colspan="7" class="text-center py-8 text-gray-400">Loading...</td></tr></tbody>
            </table>
        </div>
    </div>

    <!-- Exam Results -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100"><h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-laptop-code mr-2 text-indigo-600"></i>Exam Results (CBT)</h3></div>
        <div class="overflow-x-auto overflow-y-auto max-h-[calc(100vh-280px)] sm:max-h-[65vh] table-responsive">
            <table class="w-full text-sm" style="min-width: 760px;">
                <thead class="bg-gray-50"><tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500">Exam</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500">Subject</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500">Score</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500">Percentage</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500">Date</th>
                </tr></thead>
                <tbody id="exams-body"><tr><td colspan="5" class="text-center py-8 text-gray-400">Loading...</td></tr></tbody>
            </table>
        </div>
    </div>
</div>

<script>
let perfChartInst = null;

async function loadResults() {
    const data = await API.get('/api/student/results');
    if (!data || !data.success) return;
    const { scores, exams, summary } = data.data;

    // Summary
    document.getElementById('result-summary').innerHTML = [
        { l: 'Subjects Scored', v: summary.total_subjects, i: 'fa-book', c: 'blue' },
        { l: 'Average Score', v: summary.avg_score, i: 'fa-chart-bar', c: 'emerald' },
        { l: 'CBT Exams Taken', v: summary.total_exams, i: 'fa-laptop-code', c: 'indigo' },
    ].map(x => `<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4"><div class="flex items-center gap-3"><div class="w-10 h-10 bg-${x.c}-100 rounded-lg flex items-center justify-center"><i class="fas ${x.i} text-${x.c}-600"></i></div><div><div class="text-xl font-bold text-gray-900">${x.v}</div><div class="text-xs text-gray-500">${x.l}</div></div></div></div>`).join('');

    // Performance chart
    if (scores.length > 0) {
        if (perfChartInst) perfChartInst.destroy();
        const ctx = document.getElementById('perfChart').getContext('2d');
        perfChartInst = new Chart(ctx, {
            type: 'bar', data: {
                labels: scores.map(s => s.subject_code),
                datasets: [
                    { label: 'CA Score', data: scores.map(s => s.ca_score), backgroundColor: '#3b82f6', borderRadius: 4 },
                    { label: 'Exam Score', data: scores.map(s => s.exam_score), backgroundColor: '#8b5cf6', borderRadius: 4 }
                ]
            }, options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true, max: 100 } },
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // Scores table
    const gc = { A: 'bg-green-100 text-green-700', B: 'bg-blue-100 text-blue-700', C: 'bg-yellow-100 text-yellow-700', D: 'bg-orange-100 text-orange-700', E: 'bg-red-100 text-red-700', F: 'bg-red-200 text-red-800' };
    document.getElementById('scores-body').innerHTML = scores.length === 0 ?
        '<tr><td colspan="7" class="text-center py-8 text-gray-400">No scores yet</td></tr>' :
        scores.map(s => {
            const rc = s.remark === 'Pass' ? 'text-green-600' : 'text-red-600';
            return `<tr class="border-t border-gray-50 hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-900">${escapeHtml(s.subject_name)} (${escapeHtml(s.subject_code)})</td>
                <td class="px-4 py-3 text-gray-500">${escapeHtml(s.class_name)}</td>
                <td class="px-4 py-3 text-center">${s.ca_score}</td>
                <td class="px-4 py-3 text-center">${s.exam_score}</td>
                <td class="px-4 py-3 text-center font-bold">${s.total_score}</td>
                <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 text-xs rounded-full ${gc[s.grade]||'bg-gray-100'}">${s.grade}</span></td>
                <td class="px-4 py-3 text-center ${rc} font-medium">${s.remark}</td>
            </tr>`;
        }).join('');

    // Show download button only if all subjects have scores
    const allScored = scores.length > 0 && scores.every(s => parseFloat(s.total_score) > 0);
    const dlBtn = document.getElementById('download-btn');
    if (dlBtn) dlBtn.classList.toggle('hidden', !allScored);

    // Exams table
    document.getElementById('exams-body').innerHTML = exams.length === 0 ?
        '<tr><td colspan="5" class="text-center py-8 text-gray-400">No exam results yet</td></tr>' :
        exams.map(e => `<tr class="border-t border-gray-50 hover:bg-gray-50">
            <td class="px-4 py-3 font-medium text-gray-900">${escapeHtml(e.exam_title)}</td>
            <td class="px-4 py-3 text-gray-500">${escapeHtml(e.subject_name)} (${escapeHtml(e.subject_code)})</td>
            <td class="px-4 py-3 text-center font-bold">${e.score}/${e.total_marks}</td>
            <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 text-xs rounded-full ${parseFloat(e.percentage) >= 50 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">${e.percentage}%</span></td>
            <td class="px-4 py-3 text-center text-gray-500">${formatDate(e.end_time)}</td>
        </tr>`).join('');
}

function loadScript(src) {
    return new Promise((resolve, reject) => {
        if (document.querySelector(`script[src="${src}"]`)) { resolve(); return; }
        const s = document.createElement('script');
        s.src = src; s.onload = resolve; s.onerror = reject;
        document.head.appendChild(s);
    });
}

async function downloadResult() {
    const btn = document.getElementById('download-btn');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generating PDF...';

    try {
        // Load jsPDF and autoTable
        await loadScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js');
        await loadScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js');

        const data = await API.get('/api/student/results');
        if (!data?.success) { Toast.error('Could not load results.'); return; }
        const profileData = await API.get('/api/profile');
        const student = profileData?.data || {};
        const { scores, summary } = data.data;
        if (!scores.length) { Toast.error('No results to download.'); return; }

        const totalCA = scores.reduce((s,r) => s + parseFloat(r.ca_score||0), 0);
        const totalExam = scores.reduce((s,r) => s + parseFloat(r.exam_score||0), 0);
        const totalScore = scores.reduce((s,r) => s + parseFloat(r.total_score||0), 0);
        const avgScore = (totalScore / scores.length).toFixed(1);
        const passed = scores.filter(s => s.remark === 'Pass').length;
        const failed = scores.length - passed;

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('p', 'mm', 'a4');
        const pw = doc.internal.pageSize.getWidth();
        const ph = doc.internal.pageSize.getHeight();

        // Load logo image
        let logoImg = null;
        try {
            const img = new Image();
            img.crossOrigin = 'anonymous';
            await new Promise((resolve, reject) => {
                img.onload = resolve;
                img.onerror = reject;
                img.src = APP_URL + '/assets/images/agit-logo.png';
            });
            const canvas = document.createElement('canvas');
            canvas.width = img.naturalWidth;
            canvas.height = img.naturalHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0);
            logoImg = canvas.toDataURL('image/png');
        } catch(e) { console.warn('Could not load logo for PDF:', e); }

        // Watermark
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(60);
        doc.setTextColor(30, 64, 175);
        doc.saveGraphicsState();
        doc.setGState(new doc.GState({ opacity: 0.04 }));
        const cx = pw / 2, cy = ph / 2;
        doc.text('AGIT ACADEMY', cx, cy, { align: 'center', angle: 35 });
        doc.restoreGraphicsState();

        // Header border line
        doc.setDrawColor(30, 64, 175);
        doc.setLineWidth(0.8);
        doc.line(20, 12, pw - 20, 12);

        // Logo + Header
        let headerTextX = pw / 2;
        if (logoImg) {
            const logoW = 18, logoH = 18;
            doc.addImage(logoImg, 'PNG', 20, 14, logoW, logoH);
            headerTextX = pw / 2 + 5;
        }

        doc.setFont('helvetica', 'bold');
        doc.setFontSize(22);
        doc.setTextColor(30, 64, 175);
        doc.text('AGIT IT ACADEMY', headerTextX, 22, { align: 'center' });

        doc.setFont('helvetica', 'normal');
        doc.setFontSize(10);
        doc.setTextColor(71, 85, 105);
        doc.text('Excellence in Education', headerTextX, 28, { align: 'center' });

        // Doc title badge
        doc.setFillColor(239, 246, 255);
        doc.roundedRect(pw / 2 - 35, 34, 70, 9, 2, 2, 'F');
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(11);
        doc.setTextColor(30, 58, 138);
        doc.text('Academic Result Sheet', pw / 2, 40, { align: 'center' });

        // Double line under header
        doc.setDrawColor(30, 64, 175);
        doc.setLineWidth(0.5);
        doc.line(20, 46, pw - 20, 46);
        doc.setLineWidth(0.2);
        doc.line(20, 47.5, pw - 20, 47.5);

        // Student info
        let y = 55;
        doc.setFontSize(8);
        doc.setTextColor(100, 116, 139);
        doc.setFont('helvetica', 'normal');
        doc.text('STUDENT NAME', 20, y);
        doc.text('MATRIC NUMBER', pw / 2 - 15, y);
        doc.text('CLASS', pw - 50, y);
        y += 5;
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(11);
        doc.setTextColor(30, 41, 59);
        doc.text(student.name || 'N/A', 20, y);
        doc.text(student.matric_no || 'N/A', pw / 2 - 15, y);
        doc.text(student.class_name || 'N/A', pw - 50, y);

        // Stats boxes
        y += 10;
        const stats = [
            { label: 'Average', value: avgScore },
            { label: 'Subjects', value: String(scores.length) },
            { label: 'Passed', value: String(passed) },
            { label: 'Failed', value: String(failed) }
        ];
        const boxW = 38, boxH = 16, gap = 6;
        const startX = (pw - (stats.length * boxW + (stats.length - 1) * gap)) / 2;
        stats.forEach((st, i) => {
            const bx = startX + i * (boxW + gap);
            doc.setFillColor(241, 245, 249);
            doc.roundedRect(bx, y, boxW, boxH, 2, 2, 'F');
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(14);
            doc.setTextColor(30, 64, 175);
            doc.text(st.value, bx + boxW / 2, y + 7, { align: 'center' });
            doc.setFont('helvetica', 'normal');
            doc.setFontSize(7);
            doc.setTextColor(100, 116, 139);
            doc.text(st.label.toUpperCase(), bx + boxW / 2, y + 13, { align: 'center' });
        });

        y += boxH + 6;

        // Scores table
        const tableBody = scores.map((s, i) => [
            String(i + 1),
            `${s.subject_name} (${s.subject_code})`,
            String(s.ca_score),
            String(s.exam_score),
            String(s.total_score),
            s.grade,
            s.remark
        ]);

        doc.autoTable({
            startY: y,
            head: [['S/N', 'Subject', 'CA (40)', 'Exam (60)', 'Total (100)', 'Grade', 'Remark']],
            body: tableBody,
            foot: [['', 'TOTAL / AVERAGE', totalCA.toFixed(0), totalExam.toFixed(0), totalScore.toFixed(0) + ' (Avg: ' + avgScore + ')', '', passed + '/' + scores.length + ' Passed']],
            theme: 'grid',
            headStyles: {
                fillColor: [30, 64, 175],
                textColor: 255,
                fontStyle: 'bold',
                fontSize: 8,
                halign: 'center',
                cellPadding: 3
            },
            footStyles: {
                fillColor: [30, 58, 138],
                textColor: 255,
                fontStyle: 'bold',
                fontSize: 8,
                halign: 'center',
                cellPadding: 3
            },
            bodyStyles: {
                fontSize: 9,
                cellPadding: 2.5,
                halign: 'center'
            },
            columnStyles: {
                0: { halign: 'center', cellWidth: 12 },
                1: { halign: 'left', cellWidth: 'auto' },
                2: { halign: 'center', cellWidth: 20 },
                3: { halign: 'center', cellWidth: 20 },
                4: { halign: 'center', cellWidth: 25 },
                5: { halign: 'center', cellWidth: 18 },
                6: { halign: 'center', cellWidth: 22 }
            },
            alternateRowStyles: { fillColor: [248, 250, 252] },
            margin: { left: 20, right: 20 },
            didParseCell: function(data) {
                if (data.section === 'body' && data.column.index === 6) {
                    if (data.cell.raw === 'Pass') {
                        data.cell.styles.textColor = [22, 163, 74];
                        data.cell.styles.fontStyle = 'bold';
                    } else {
                        data.cell.styles.textColor = [220, 38, 38];
                        data.cell.styles.fontStyle = 'bold';
                    }
                }
                if (data.section === 'body' && data.column.index === 5) {
                    const gradeColors = {
                        'A': [22, 101, 52], 'B': [30, 64, 175], 'C': [133, 77, 14],
                        'D': [154, 52, 18], 'E': [153, 27, 27], 'F': [153, 27, 27]
                    };
                    const c = gradeColors[data.cell.raw];
                    if (c) {
                        data.cell.styles.textColor = c;
                        data.cell.styles.fontStyle = 'bold';
                    }
                }
                if (data.section === 'body' && data.column.index === 4) {
                    data.cell.styles.fontStyle = 'bold';
                }
            }
        });

        // Footer
        const finalY = doc.lastAutoTable.finalY + 15;

        // Divider
        doc.setDrawColor(226, 232, 240);
        doc.setLineWidth(0.3);
        doc.line(20, finalY - 5, pw - 20, finalY - 5);

        // Date
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(9);
        doc.setTextColor(100, 116, 139);
        doc.text('Date Issued: ' + new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }), 20, finalY);

        // Signature
        doc.setDrawColor(30, 64, 175);
        doc.setLineWidth(0.5);
        doc.line(pw - 70, finalY + 10, pw - 20, finalY + 10);
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(9);
        doc.setTextColor(30, 64, 175);
        doc.text("Registrar's Signature", pw - 45, finalY + 15, { align: 'center' });

        // Bottom border
        doc.setDrawColor(30, 64, 175);
        doc.setLineWidth(0.8);
        doc.line(20, ph - 10, pw - 20, ph - 10);

        // Save
        const filename = 'Result_Sheet_' + (student.matric_no || 'student').replace(/[/\\]/g, '_') + '.pdf';
        doc.save(filename);
        Toast.success('PDF result downloaded successfully!');
    } catch (err) {
        console.error('PDF generation error:', err);
        Toast.error('Failed to generate PDF. Please try again.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-download mr-2"></i>Download Result';
    }
}

document.addEventListener('DOMContentLoaded', loadResults);
</script>
