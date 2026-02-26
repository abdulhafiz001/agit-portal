<?php
$pageTitle = 'New Registrations';
?>
<div class="p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">New Student Registrations</h1>
            <p class="text-gray-500 text-sm mt-1">Review and approve or decline pending student applications</p>
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
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Pending Approvals</h3>
            <p class="text-sm text-gray-500 mt-1">Students awaiting admin approval</p>
        </div>
        <div id="pending-list" class="p-6">
            <div class="text-center py-12 text-gray-400"><div class="spinner mx-auto mb-3"></div>Loading...</div>
        </div>
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
