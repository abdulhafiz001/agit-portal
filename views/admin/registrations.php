<?php
$pageTitle = 'New Registrations';
?>
<div class="p-6 space-y-6">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold tracking-wide uppercase">
                    <i class="fas fa-user-check text-[11px]"></i>
                    Admissions Queue
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mt-4">New Student Registrations</h1>
                <p class="text-gray-600 text-sm mt-2 max-w-2xl">
                    Review pending applications, confirm whether the student's email has been verified, and assign the correct class before approval.
                </p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm min-w-full lg:min-w-[360px]">
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-700">
                    <div class="font-semibold flex items-center gap-2">
                        <i class="fas fa-envelope-circle-check"></i>
                        Verified Email
                    </div>
                    <p class="mt-1 text-emerald-600">Student completed email verification and is ready for review.</p>
                </div>
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-amber-700">
                    <div class="font-semibold flex items-center gap-2">
                        <i class="fas fa-envelope-open-text"></i>
                        Unverified Email
                    </div>
                    <p class="mt-1 text-amber-600">Student registered but has not yet completed email verification.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                    <i class="fas fa-clock text-amber-600"></i>
                </div>
                <div>
                    <div id="stat-pending" class="text-2xl font-bold text-gray-900">-</div>
                    <div class="text-sm text-gray-500">Pending</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                    <i class="fas fa-check text-green-600"></i>
                </div>
                <div>
                    <div id="stat-approved" class="text-2xl font-bold text-gray-900">-</div>
                    <div class="text-sm text-gray-500">Approved</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
                    <i class="fas fa-times text-red-600"></i>
                </div>
                <div>
                    <div id="stat-rejected" class="text-2xl font-bold text-gray-900">-</div>
                    <div class="text-sm text-gray-500">Rejected</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 col-span-2 md:col-span-1">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-chart-pie text-blue-600"></i>
                </div>
                <div>
                    <div id="stat-total" class="text-2xl font-bold text-gray-900">-</div>
                    <div class="text-sm text-gray-500">Total</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart: Registrations by Class -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Registrations by Class</h3>
        <div class="h-64">
            <canvas id="chart-by-class"></canvas>
        </div>
    </div>

    <!-- Pending List -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200 bg-gray-50/70">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Pending Approvals</h3>
                    <p class="text-sm text-gray-500 mt-1">Students awaiting admin approval and class assignment</p>
                </div>
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <i class="fas fa-circle text-[8px]"></i>
                        Email verified
                    </span>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                        <i class="fas fa-circle text-[8px]"></i>
                        Email unverified
                    </span>
                </div>
            </div>
        </div>
        <div id="pending-list" class="p-6">
            <div class="text-center py-12 text-gray-400"><div class="spinner mx-auto mb-3"></div>Loading...</div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approve-modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">Approve Student</h3>
            <p class="text-sm text-gray-500 mt-1">Assign a class to this student before approving. They will be notified with their class and courses.</p>
        </div>
        <form id="approve-form" class="p-6">
            <input type="hidden" id="approve-id" value="">
            <div class="mb-2">
                <p class="text-sm font-medium text-gray-700 mb-1">Student</p>
                <p id="approve-student-name" class="text-sm text-gray-900 font-semibold"></p>
                <p id="approve-student-email" class="text-sm text-gray-500"></p>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Assign Class <span class="text-red-500">*</span></label>
                <select id="approve-class" required class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 outline-none appearance-none cursor-pointer bg-white">
                    <option value="">Select a class...</option>
                </select>
            </div>
            <div id="approve-courses-preview" class="mt-4 hidden">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Courses in this class</p>
                <div id="approve-courses-list" class="flex flex-wrap gap-2"></div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="button" onclick="Modal.close('approve-modal')" class="flex-1 py-2.5 border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">Cancel</button>
                <button type="submit" id="approve-submit-btn" class="flex-1 py-2.5 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700"><i class="fas fa-check mr-1"></i>Approve & Assign</button>
            </div>
        </form>
    </div>
</div>

<!-- Decline Modal -->
<div id="decline-modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">Decline Application</h3>
            <p class="text-sm text-gray-500 mt-1">Provide a reason (required). The student will receive this via email.</p>
        </div>
        <form id="decline-form" class="p-6">
            <input type="hidden" id="decline-id" value="">
            <p class="text-sm text-gray-600 mb-4">Reason for rejection *</p>
            <textarea id="decline-reason" required minlength="10" rows="4" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="e.g. Class is full for this intake..."></textarea>
            <div class="flex gap-3 mt-4">
                <button type="button" onclick="Modal.close('decline-modal')" class="flex-1 py-2.5 border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700">Decline & Notify</button>
            </div>
        </form>
    </div>
</div>
