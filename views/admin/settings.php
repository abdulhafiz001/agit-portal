<!-- Phase 3: Settings + Grading + Promotions + Activity Logs -->

<!-- Tab Navigation -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
    <!-- Desktop tabs -->
    <div class="hidden sm:flex overflow-x-auto border-b border-gray-200 scrollbar-hide" style="-ms-overflow-style:none;scrollbar-width:none;">
        <button onclick="switchTab('general')" id="tab-general" class="settings-tab active flex items-center gap-2 px-5 py-3.5 text-sm font-medium border-b-2 whitespace-nowrap">
            <i class="fas fa-cog"></i> General
        </button>
        <button onclick="switchTab('grading')" id="tab-grading" class="settings-tab flex items-center gap-2 px-5 py-3.5 text-sm font-medium border-b-2 whitespace-nowrap">
            <i class="fas fa-star-half-alt"></i> Grading
        </button>
        <button onclick="switchTab('promotions')" id="tab-promotions" class="settings-tab flex items-center gap-2 px-5 py-3.5 text-sm font-medium border-b-2 whitespace-nowrap">
            <i class="fas fa-level-up-alt"></i> Promotions
        </button>
        <button onclick="switchTab('logs')" id="tab-logs" class="settings-tab flex items-center gap-2 px-5 py-3.5 text-sm font-medium border-b-2 whitespace-nowrap">
            <i class="fas fa-history"></i> Logs
        </button>
        <button onclick="switchTab('admins')" id="tab-admins" class="settings-tab flex items-center gap-2 px-5 py-3.5 text-sm font-medium border-b-2 whitespace-nowrap">
            <i class="fas fa-users-cog"></i> Admins
        </button>
        <button onclick="switchTab('cms')" id="tab-cms" class="settings-tab flex items-center gap-2 px-5 py-3.5 text-sm font-medium border-b-2 whitespace-nowrap">
            <i class="fas fa-globe"></i> Landing Page CMS
        </button>
        <button onclick="switchTab('email')" id="tab-email" class="settings-tab flex items-center gap-2 px-5 py-3.5 text-sm font-medium border-b-2 whitespace-nowrap">
            <i class="fas fa-envelope"></i> Email / SMTP
        </button>
    </div>
    <!-- Mobile tab dropdown -->
    <div class="sm:hidden p-3">
        <select id="mobile-tab-select" onchange="switchTab(this.value)" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm font-medium bg-white">
            <option value="general">General Settings</option>
            <option value="grading">Grading Config</option>
            <option value="promotions">Promotions</option>
            <option value="logs">Activity Logs</option>
            <option value="admins">Manage Admins</option>
            <option value="cms">Landing Page CMS</option>
            <option value="email">Email / SMTP</option>
        </select>
    </div>
</div>

<!-- ==================== GENERAL SETTINGS TAB ==================== -->
<div id="panel-general" class="tab-panel">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Portal Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-university text-blue-500"></i> Portal Information</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Institution Name</label>
                    <input type="text" id="set-school_name" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm" placeholder="e.g. AGIT Academy">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Abbreviation / Short Name</label>
                    <input type="text" id="set-school_abbr" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm" placeholder="e.g. AGIT">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input type="text" id="set-school_address" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm" placeholder="Institution address">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" id="set-school_phone" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm" placeholder="+234...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="set-school_email" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm" placeholder="admin@school.com">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                    <input type="url" id="set-school_website" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm" placeholder="https://...">
                </div>
            </div>
        </div>

        <!-- Academic Config -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-graduation-cap text-green-500"></i> Academic Configuration</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Grading System</label>
                    <select id="set-grading_system" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm">
                        <option value="percentage">Percentage Based</option>
                        <option value="gpa">GPA Based (4.0)</option>
                        <option value="gpa5">GPA Based (5.0)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Score Components</label>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-xs text-gray-500">CA Weight (%)</label>
                            <input type="number" id="set-ca_weight" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" value="40" min="0" max="100">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Exam Weight (%)</label>
                            <input type="number" id="set-exam_weight" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" value="60" min="0" max="100">
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pass Mark (%)</label>
                    <input type="number" id="set-pass_mark" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm" value="50" min="0" max="100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Score per Subject</label>
                    <input type="number" id="set-max_score" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm" value="100" min="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Default Student Password</label>
                    <input type="text" id="set-default_password" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm" value="password">
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Allow Student Self-Registration</label>
                        <p class="text-xs text-gray-400">Students can create their own accounts</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="set-allow_registration" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Exam Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-file-alt text-purple-500"></i> Exam Settings</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Require Admin Approval</label>
                        <p class="text-xs text-gray-400">Exams must be approved before they start</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="set-exam_require_approval" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Anti-Cheat (Auto-submit)</label>
                        <p class="text-xs text-gray-400">Auto-submit exam if student leaves page</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="set-exam_anti_cheat" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Full-Screen Mode</label>
                        <p class="text-xs text-gray-400">Exam opens in full-screen for students</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="set-exam_fullscreen" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Exam Duration (minutes)</label>
                    <input type="number" id="set-max_exam_duration" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm" value="180" min="10">
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-bell text-yellow-500"></i> Notifications & Display</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Show Announcements on Dashboard</label>
                        <p class="text-xs text-gray-400">Display latest announcements</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="set-show_announcements" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Records Per Page</label>
                    <select id="set-records_per_page" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Format</label>
                    <select id="set-date_format" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm">
                        <option value="d/m/Y">DD/MM/YYYY</option>
                        <option value="m/d/Y">MM/DD/YYYY</option>
                        <option value="Y-m-d">YYYY-MM-DD</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Portal Motto / Tagline</label>
                    <input type="text" id="set-school_motto" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm" placeholder="e.g. Excellence in Education">
                </div>
            </div>
        </div>
    </div>
    <div class="mt-6 flex justify-end">
        <button onclick="saveGeneralSettings()" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 flex items-center gap-2">
            <i class="fas fa-save"></i> Save Settings
        </button>
    </div>
</div>

<!-- ==================== GRADING CONFIG TAB ==================== -->
<div id="panel-grading" class="tab-panel hidden">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Create / Edit Grading -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-plus-circle text-green-500"></i> Create Grading Scale</h3>
                <div class="space-y-4">
                    <div id="grade-rows" class="space-y-3">
                        <!-- Dynamic rows added here -->
                    </div>
                    <button onclick="addGradeRow()" class="w-full py-2 border-2 border-dashed border-gray-300 rounded-lg text-sm text-gray-500 hover:border-blue-400 hover:text-blue-500 flex items-center justify-center gap-2">
                        <i class="fas fa-plus"></i> Add Grade Level
                    </button>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assign to Classes</label>
                        <div id="grading-classes-list" class="space-y-2 max-h-40 overflow-y-auto">
                            <span class="text-xs text-gray-400">Loading...</span>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <button onclick="saveGradingConfig()" class="flex-1 py-2.5 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">
                            <i class="fas fa-save"></i> Save Config
                        </button>
                        <button onclick="resetGradingForm()" class="py-2.5 px-4 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200">
                            <i class="fas fa-undo"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Existing Configs -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-list text-blue-500"></i> Existing Grading Configurations</h3>
                <div id="grading-configs-list" class="space-y-4">
                    <div class="text-center py-8 text-gray-400"><div class="spinner mx-auto mb-3"></div>Loading...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==================== PROMOTIONS TAB ==================== -->
<div id="panel-promotions" class="tab-panel hidden">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Create Rule -->
        <div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-gavel text-orange-500"></i> Promotion Rules</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rule Name</label>
                        <input type="text" id="rule-name" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm" placeholder="e.g. Minimum Average Score">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rule Type</label>
                        <select id="rule-type" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm">
                            <option value="min_average">Minimum Average Score</option>
                            <option value="min_pass_subjects">Minimum Passed Subjects</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                        <input type="number" id="rule-value" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm" placeholder="e.g. 50" step="0.01">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Apply to Class <span class="text-gray-400">(leave empty for all)</span></label>
                        <select id="rule-class" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm">
                            <option value="">All Classes</option>
                        </select>
                    </div>
                    <button onclick="savePromotionRule()" class="w-full py-2.5 bg-orange-600 text-white rounded-lg text-sm font-medium hover:bg-orange-700 flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Save Rule
                    </button>
                </div>

                <hr class="my-6">

                <h4 class="font-semibold text-gray-800 mb-3">Active Rules</h4>
                <div id="rules-list" class="space-y-2">
                    <span class="text-xs text-gray-400">Loading...</span>
                </div>
            </div>
        </div>

        <!-- Process Promotions -->
        <div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-rocket text-blue-500"></i> Process Promotions</h3>
                <p class="text-sm text-gray-500 mb-4">Select a source class and a target class. Students meeting all active promotion rules will be moved to the target class.</p>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Source Class (Current)</label>
                        <select id="promo-source" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm">
                            <option value="">Select source class</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Semester (optional)</label>
                        <select id="promo-semester" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm">
                            <option value="">All Semesters</option>
                            <option value="first">First Semester</option>
                            <option value="second">Second Semester</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Target Class (Promote To)</label>
                        <select id="promo-target" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm">
                            <option value="">Select target class</option>
                        </select>
                    </div>
                    <button onclick="processPromotions()" class="w-full py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 flex items-center justify-center gap-2">
                        <i class="fas fa-play"></i> Run Promotion
                    </button>
                </div>

                <!-- Promotion Results -->
                <div id="promo-results" class="hidden mt-6">
                    <h4 class="font-semibold text-gray-800 mb-3">Results</h4>
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="bg-green-50 rounded-lg p-3 text-center">
                            <div id="promo-promoted" class="text-2xl font-bold text-green-600">0</div>
                            <div class="text-xs text-green-700">Promoted</div>
                        </div>
                        <div class="bg-red-50 rounded-lg p-3 text-center">
                            <div id="promo-retained" class="text-2xl font-bold text-red-600">0</div>
                            <div class="text-xs text-red-700">Retained</div>
                        </div>
                    </div>
                    <div class="table-responsive max-h-72 overflow-y-auto">
                        <table class="data-table">
                            <thead><tr><th>Student</th><th>Matric No</th><th>Avg Score</th><th>Status</th></tr></thead>
                            <tbody id="promo-details"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==================== ACTIVITY LOGS TAB ==================== -->
<div id="panel-logs" class="tab-panel hidden">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2"><i class="fas fa-history text-indigo-500"></i> Activity Logs</h3>
            <div class="flex gap-3">
                <select id="log-filter" onchange="loadActivityLogs()" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    <option value="">All Users</option>
                    <option value="admin">Admins</option>
                    <option value="lecturer">Lecturers</option>
                    <option value="student">Students</option>
                </select>
            </div>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>User Type</th>
                        <th>User ID</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody id="logs-tbody">
                    <tr><td colspan="6" class="text-center py-8 text-gray-400">Loading...</td></tr>
                </tbody>
            </table>
        </div>
        <div id="logs-pagination" class="p-4 border-t border-gray-200 flex items-center justify-between text-sm text-gray-500"></div>
    </div>
</div>

<!-- ==================== LANDING PAGE CMS TAB ==================== -->
<div id="panel-cms" class="tab-panel hidden">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-2 flex items-center gap-2"><i class="fas fa-book text-blue-500"></i> Courses on Landing Page</h3>
        <p class="text-sm text-gray-500 mb-4">Select which courses to display on the public landing page. Toggle ON to show, OFF to hide.</p>
        <div id="cms-courses-list" class="space-y-3">
            <div class="text-center py-8 text-gray-400"><div class="spinner mx-auto mb-3"></div>Loading courses...</div>
        </div>
        <div class="mt-4 flex justify-end">
            <button onclick="saveCmsCourses()" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700"><i class="fas fa-save mr-2"></i>Save Display Settings</button>
        </div>
    </div>
</div>

<!-- ==================== EMAIL / SMTP TAB ==================== -->
<div id="panel-email" class="tab-panel hidden">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 max-w-2xl">
        <h3 class="text-lg font-semibold text-gray-900 mb-2 flex items-center gap-2"><i class="fas fa-envelope text-green-500"></i> SMTP Configuration</h3>
        <p class="text-sm text-gray-500 mb-4">Configure SMTP for contact form and password reset emails. Port 465 uses SSL.</p>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Form Recipient Email</label>
                <input type="email" id="set-contact_email" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm" placeholder="admin@agitacademy.com">
                <p class="text-xs text-gray-500 mt-1">New student registrations, contact form, and other notifications are sent to this address.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Host</label>
                <input type="text" id="set-smtp_host" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm" placeholder="smtp.gmail.com">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Username (Email)</label>
                    <input type="text" id="set-smtp_username" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm" placeholder="your@email.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Password</label>
                    <div class="relative">
                        <input type="password" id="set-smtp_password" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm pr-10" placeholder="App password">
                        <span id="smtp-password-set" class="absolute right-3 top-1/2 -translate-y-1/2 text-green-500 hidden" title="Password is configured"><i class="fas fa-check-circle"></i></span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Leave blank to keep current. Set to show configured.</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Port</label>
                    <input type="number" id="set-smtp_port" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm" value="465" placeholder="465">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Encryption</label>
                    <select id="set-smtp_encryption" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm">
                        <option value="ssl">SSL (port 465)</option>
                        <option value="tls">TLS (port 587)</option>
                    </select>
                </div>
            </div>
            <div class="bg-amber-50 rounded-lg p-3 text-sm text-amber-800">
                <strong>Gmail:</strong> Use an <a href="https://myaccount.google.com/apppasswords" target="_blank" rel="noopener" class="underline">App Password</a> (not your regular password). Enable 2-Step Verification first. Port 465 = SSL, Port 587 = TLS.
            </div>
            <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-end pt-2">
                <div class="flex-1 min-w-0">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Test Email</label>
                    <input type="email" id="test-email-to" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm" placeholder="your@email.com">
                    <p class="text-xs text-gray-500 mt-1">Send a test email to verify SMTP is working.</p>
                </div>
                <button onclick="sendTestEmail()" class="px-4 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 whitespace-nowrap"><i class="fas fa-paper-plane mr-2"></i>Send Test Email</button>
            </div>
        </div>
        <div class="mt-4 flex justify-end">
            <button onclick="saveSmtpSettings()" class="px-6 py-2.5 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700"><i class="fas fa-save mr-2"></i>Save SMTP Settings</button>
        </div>
    </div>
</div>

<!-- ==================== MANAGE ADMINS TAB ==================== -->
<div id="panel-admins" class="tab-panel hidden">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2"><i class="fas fa-users-cog text-purple-500"></i> Admin Accounts</h3>
            <button onclick="showAddAdmin()" class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700"><i class="fas fa-plus mr-2"></i>Add Admin</button>
        </div>
        <div id="admins-list" class="p-6">
            <div class="text-center py-8 text-gray-400"><div class="spinner mx-auto mb-3"></div>Loading...</div>
        </div>
    </div>
</div>

<!-- Add Admin Modal -->
<div id="admin-modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] flex flex-col">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
            <h3 id="admin-modal-title" class="text-lg font-semibold text-gray-900">Add Admin</h3>
            <button onclick="Modal.close('admin-modal')" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"><i class="fas fa-times"></i></button>
        </div>
        <form id="admin-form" class="p-6 space-y-4 overflow-y-auto flex-1">
            <input type="hidden" id="adm-id" value="">
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" id="adm-name" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" id="adm-email" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm"></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500" id="pw-req">*</span></label>
                    <input type="password" id="adm-password" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm" placeholder="Min 6 characters"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select id="adm-role" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm">
                        <option value="complete">Full Access</option>
                        <option value="limited">Limited Access</option>
                    </select></div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Accessible Pages <span class="text-gray-400 text-xs">(for limited access)</span></label>
                <div id="pages-grid" class="grid grid-cols-2 gap-2">
                    <label class="flex items-center gap-2 text-sm p-2 bg-gray-50 rounded-lg"><input type="checkbox" value="dashboard" class="adm-page rounded border-gray-300 text-purple-600" checked disabled> Dashboard</label>
                    <label class="flex items-center gap-2 text-sm p-2 bg-gray-50 rounded-lg"><input type="checkbox" value="registrations" class="adm-page rounded border-gray-300 text-purple-600"> New Registrations</label>
                    <label class="flex items-center gap-2 text-sm p-2 bg-gray-50 rounded-lg"><input type="checkbox" value="students" class="adm-page rounded border-gray-300 text-purple-600"> Students</label>
                    <label class="flex items-center gap-2 text-sm p-2 bg-gray-50 rounded-lg"><input type="checkbox" value="lecturers" class="adm-page rounded border-gray-300 text-purple-600"> Lecturers</label>
                    <label class="flex items-center gap-2 text-sm p-2 bg-gray-50 rounded-lg"><input type="checkbox" value="subjects" class="adm-page rounded border-gray-300 text-purple-600"> Courses</label>
                    <label class="flex items-center gap-2 text-sm p-2 bg-gray-50 rounded-lg"><input type="checkbox" value="classes" class="adm-page rounded border-gray-300 text-purple-600"> Classes</label>
                    <label class="flex items-center gap-2 text-sm p-2 bg-gray-50 rounded-lg"><input type="checkbox" value="schedules" class="adm-page rounded border-gray-300 text-purple-600"> Schedules</label>
                    <label class="flex items-center gap-2 text-sm p-2 bg-gray-50 rounded-lg"><input type="checkbox" value="exams" class="adm-page rounded border-gray-300 text-purple-600"> Exams</label>
                    <label class="flex items-center gap-2 text-sm p-2 bg-gray-50 rounded-lg"><input type="checkbox" value="results" class="adm-page rounded border-gray-300 text-purple-600"> Results</label>
                    <label class="flex items-center gap-2 text-sm p-2 bg-gray-50 rounded-lg"><input type="checkbox" value="reports" class="adm-page rounded border-gray-300 text-purple-600"> Reports</label>
                    <label class="flex items-center gap-2 text-sm p-2 bg-gray-50 rounded-lg"><input type="checkbox" value="announcements" class="adm-page rounded border-gray-300 text-purple-600"> Announcements</label>
                    <label class="flex items-center gap-2 text-sm p-2 bg-gray-50 rounded-lg"><input type="checkbox" value="settings" class="adm-page rounded border-gray-300 text-purple-600"> Settings</label>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="Modal.close('admin-modal')" class="flex-1 py-2.5 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">Cancel</button>
                <button type="submit" id="save-admin-btn" class="flex-1 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm font-medium">Save</button>
            </div>
        </form>
    </div>
</div>

<style>
    .settings-tab { color: #64748b; border-color: transparent; }
    .settings-tab:hover { color: #334155; background: #f8fafc; }
    .settings-tab.active { color: #2563eb; border-color: #2563eb; background: #eff6ff; }
    @media (max-width: 640px) {
        .settings-tab { padding: 0.5rem 1rem; font-size: 0.75rem; }
        .settings-tab i { display: none; }
    }
</style>

<script>
let classesCache = [];
let currentGradingGroup = null;
let logsPage = 1;

// ==================== TAB SWITCHING ====================
function switchTab(tab) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.settings-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('panel-' + tab).classList.remove('hidden');
    const tabBtn = document.getElementById('tab-' + tab);
    if (tabBtn) tabBtn.classList.add('active');
    // Sync mobile dropdown
    const mobileSelect = document.getElementById('mobile-tab-select');
    if (mobileSelect) mobileSelect.value = tab;

    if (tab === 'grading') loadGradingConfigs();
    if (tab === 'promotions') loadPromotionData();
    if (tab === 'logs') loadActivityLogs();
    if (tab === 'admins') loadAdmins();
    if (tab === 'cms') loadCmsCourses();
    if (tab === 'email') loadSmtpSettings();
}

// ==================== GENERAL SETTINGS ====================
async function loadGeneralSettings() {
    const data = await API.get('/api/admin/settings');
    if (!data || !data.success) return;
    const all = {};
    data.raw.forEach(s => { all[s.setting_key] = s.setting_value; });

    // Populate fields
    const fields = ['school_name','school_abbr','school_address','school_phone','school_email','school_website',
                     'grading_system','ca_weight','exam_weight','pass_mark','max_score','default_password',
                     'max_exam_duration','records_per_page','date_format','school_motto'];
    fields.forEach(key => {
        const el = document.getElementById('set-' + key);
        if (el && all[key] !== undefined) {
            if (el.tagName === 'SELECT') el.value = all[key];
            else el.value = all[key];
        }
    });

    // Checkboxes
    const checks = ['allow_registration','exam_require_approval','exam_anti_cheat','exam_fullscreen','show_announcements'];
    checks.forEach(key => {
        const el = document.getElementById('set-' + key);
        if (el && all[key] !== undefined) el.checked = all[key] === '1' || all[key] === 'true';
    });
}

async function saveGeneralSettings() {
    const fields = ['school_name','school_abbr','school_address','school_phone','school_email','school_website',
                     'grading_system','ca_weight','exam_weight','pass_mark','max_score','default_password',
                     'max_exam_duration','records_per_page','date_format','school_motto'];
    const checks = ['allow_registration','exam_require_approval','exam_anti_cheat','exam_fullscreen','show_announcements'];

    const settings = [];
    fields.forEach(key => {
        const el = document.getElementById('set-' + key);
        if (el) settings.push({ key, value: el.value, category: key.startsWith('exam_') ? 'exam' : key.startsWith('school_') ? 'general' : 'academic' });
    });
    checks.forEach(key => {
        const el = document.getElementById('set-' + key);
        if (el) settings.push({ key, value: el.checked ? '1' : '0', category: key.startsWith('exam_') ? 'exam' : 'general' });
    });

    const data = await API.post('/api/admin/settings', { settings });
    if (data && data.success) Toast.success(data.message);
}

// ==================== GRADING CONFIG ====================
function addGradeRow(grade = '', minScore = '', maxScore = '', remark = '') {
    const container = document.getElementById('grade-rows');
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 items-center';
    row.innerHTML = `
        <input type="text" class="col-span-2 px-2 py-2 border border-gray-200 rounded-lg text-sm text-center font-bold" placeholder="A" value="${grade}">
        <input type="number" class="col-span-3 px-2 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Min %" value="${minScore}" step="0.01">
        <input type="number" class="col-span-3 px-2 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Max %" value="${maxScore}" step="0.01">
        <input type="text" class="col-span-3 px-2 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Remark" value="${remark}">
        <button onclick="this.parentElement.remove()" class="col-span-1 p-2 text-red-400 hover:text-red-600 flex items-center justify-center"><i class="fas fa-times"></i></button>
    `;
    container.appendChild(row);
}

async function loadClassesForGrading() {
    const data = await API.get('/api/admin/classes');
    if (!data || !data.success) return;
    classesCache = data.data;
    const container = document.getElementById('grading-classes-list');
    container.innerHTML = classesCache.map(c => `
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" value="${c.id}" class="grading-class-check rounded border-gray-300 text-blue-600">
            <span>${c.name}</span>
        </label>
    `).join('');

    // Also fill promotion dropdowns
    ['rule-class','promo-source','promo-target'].forEach(id => {
        const sel = document.getElementById(id);
        const first = sel.options[0].outerHTML;
        sel.innerHTML = first + classesCache.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
    });
}

async function loadGradingConfigs() {
    const data = await API.get('/api/admin/grading');
    const container = document.getElementById('grading-configs-list');
    if (!data || !data.success || !data.data.length) {
        container.innerHTML = '<div class="text-center py-8 text-gray-400"><i class="fas fa-star-half-alt text-3xl mb-2"></i><p>No grading configurations yet</p></div>';
        return;
    }
    container.innerHTML = data.data.map(cfg => {
        const classNames = cfg.classes.map(c => c.class_name).join(', ') || '<span class="text-gray-400 italic">Not assigned</span>';
        return `
        <div class="border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-semibold text-gray-800">Config Group #${cfg.group}</h4>
                <div class="flex gap-2">
                    <button onclick="editGradingConfig(${JSON.stringify(cfg).replace(/"/g, '&quot;')})" class="text-blue-500 hover:text-blue-700 text-sm"><i class="fas fa-edit"></i> Edit</button>
                    <button onclick="deleteGradingConfig(${cfg.group})" class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i> Delete</button>
                </div>
            </div>
            <div class="text-xs text-gray-500 mb-2">Classes: ${classNames}</div>
            <table class="data-table text-sm">
                <thead><tr><th>Grade</th><th>Min</th><th>Max</th><th>Remark</th></tr></thead>
                <tbody>
                    ${cfg.grades.map(g => `<tr><td class="font-bold">${g.grade}</td><td>${g.min_score}</td><td>${g.max_score}</td><td>${g.remark || '-'}</td></tr>`).join('')}
                </tbody>
            </table>
        </div>`;
    }).join('');
}

async function saveGradingConfig() {
    const rows = document.querySelectorAll('#grade-rows > div');
    if (!rows.length) { Toast.error('Add at least one grade level.'); return; }

    const grades = [];
    let valid = true;
    rows.forEach(row => {
        const inputs = row.querySelectorAll('input');
        const grade = inputs[0].value.trim();
        const min = parseFloat(inputs[1].value);
        const max = parseFloat(inputs[2].value);
        const remark = inputs[3].value.trim();
        if (!grade || isNaN(min) || isNaN(max)) { valid = false; return; }
        grades.push({ grade, min_score: min, max_score: max, remark });
    });
    if (!valid) { Toast.error('Please fill in all grade fields.'); return; }

    const classIds = [...document.querySelectorAll('.grading-class-check:checked')].map(c => c.value);

    const data = await API.post('/api/admin/grading', { config_group: currentGradingGroup, grades, class_ids: classIds });
    if (data && data.success) {
        Toast.success(data.message);
        resetGradingForm();
        loadGradingConfigs();
    }
}

function editGradingConfig(cfg) {
    currentGradingGroup = cfg.group;
    const container = document.getElementById('grade-rows');
    container.innerHTML = '';
    cfg.grades.forEach(g => addGradeRow(g.grade, g.min_score, g.max_score, g.remark));
    cfg.classes.forEach(c => {
        const cb = document.querySelector(`.grading-class-check[value="${c.class_id}"]`);
        if (cb) cb.checked = true;
    });
    switchTab('grading');
}

async function deleteGradingConfig(group) {
    if (!await confirmAction('Delete this grading configuration?')) return;
    const data = await API.delete('/api/admin/grading/' + group);
    if (data && data.success) { Toast.success(data.message); loadGradingConfigs(); }
}

function resetGradingForm() {
    currentGradingGroup = null;
    document.getElementById('grade-rows').innerHTML = '';
    document.querySelectorAll('.grading-class-check').forEach(c => c.checked = false);
    addGradeRow('A', 70, 100, 'Excellent');
    addGradeRow('B', 60, 69.99, 'Very Good');
    addGradeRow('C', 50, 59.99, 'Good');
    addGradeRow('D', 45, 49.99, 'Fair');
    addGradeRow('E', 40, 44.99, 'Pass');
    addGradeRow('F', 0, 39.99, 'Fail');
}

// ==================== PROMOTION RULES ====================
async function loadPromotionData() {
    const data = await API.get('/api/admin/promotions');
    const container = document.getElementById('rules-list');
    if (!data || !data.success || !data.data.length) {
        container.innerHTML = '<p class="text-sm text-gray-400">No rules defined yet.</p>';
        return;
    }
    container.innerHTML = data.data.map(r => `
        <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3">
            <div>
                <div class="font-medium text-sm text-gray-800">${r.name}</div>
                <div class="text-xs text-gray-500">${r.rule_type === 'min_average' ? 'Min Avg:' : 'Min Passed:'} ${r.rule_value} · ${r.class_name || 'All Classes'} · <span class="badge ${r.status === 'active' ? 'badge-success' : 'badge-gray'}">${r.status}</span></div>
            </div>
            <button onclick="deletePromotionRule(${r.id})" class="text-red-400 hover:text-red-600"><i class="fas fa-trash text-xs"></i></button>
        </div>
    `).join('');
}

async function savePromotionRule() {
    const name = document.getElementById('rule-name').value.trim();
    const rule_type = document.getElementById('rule-type').value;
    const rule_value = document.getElementById('rule-value').value;
    const class_id = document.getElementById('rule-class').value;
    if (!name || !rule_value) { Toast.error('Name and value are required.'); return; }

    const data = await API.post('/api/admin/promotions', { name, rule_type, rule_value, class_id });
    if (data && data.success) {
        Toast.success(data.message);
        document.getElementById('rule-name').value = '';
        document.getElementById('rule-value').value = '';
        loadPromotionData();
    }
}

async function deletePromotionRule(id) {
    if (!await confirmAction('Delete this rule?')) return;
    const data = await API.delete('/api/admin/promotions/' + id);
    if (data && data.success) { Toast.success(data.message); loadPromotionData(); }
}

async function processPromotions() {
    const class_id = document.getElementById('promo-source').value;
    const target_class_id = document.getElementById('promo-target').value;
    const semester = document.getElementById('promo-semester').value;
    if (!class_id || !target_class_id) { Toast.error('Select source and target classes.'); return; }
    if (class_id === target_class_id) { Toast.error('Source and target must be different.'); return; }
    if (!await confirmAction('Process promotions? Students meeting all rules will be moved to the target class.')) return;

    const data = await API.post('/api/admin/promotions/process', { class_id, target_class_id, semester });
    if (data && data.success) {
        Toast.success(data.message);
        document.getElementById('promo-results').classList.remove('hidden');
        document.getElementById('promo-promoted').textContent = data.promoted;
        document.getElementById('promo-retained').textContent = data.retained;
        const tbody = document.getElementById('promo-details');
        tbody.innerHTML = (data.details || []).map(d => `
            <tr>
                <td class="font-medium">${d.name}</td>
                <td>${d.matric_no}</td>
                <td>${d.avg_score}</td>
                <td>${d.promoted ? '<span class="badge badge-success">Promoted</span>' : '<span class="badge badge-danger">Retained</span>'}</td>
            </tr>
        `).join('') || '<tr><td colspan="4" class="text-center py-4 text-gray-400">No students found.</td></tr>';
    }
}

// ==================== ACTIVITY LOGS ====================
async function loadActivityLogs(page = 1) {
    logsPage = page;
    const type = document.getElementById('log-filter').value;
    const data = await API.get('/api/admin/activity-logs?type=' + type + '&page=' + page);
    const tbody = document.getElementById('logs-tbody');
    if (!data || !data.success || !data.data.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-gray-400"><i class="fas fa-history text-3xl block mb-2"></i>No activity logs found</td></tr>';
        document.getElementById('logs-pagination').innerHTML = '';
        return;
    }
    tbody.innerHTML = data.data.map(l => `
        <tr>
            <td class="text-xs text-gray-500">${new Date(l.created_at).toLocaleString()}</td>
            <td><span class="badge ${l.user_type === 'admin' ? 'badge-info' : l.user_type === 'lecturer' ? 'badge-warning' : 'badge-success'}">${l.user_type}</span></td>
            <td>#${l.user_id}</td>
            <td class="font-medium">${l.action}</td>
            <td class="text-sm text-gray-500 max-w-xs truncate">${l.description || '-'}</td>
            <td class="text-xs text-gray-400">${l.ip_address || '-'}</td>
        </tr>
    `).join('');

    const p = data.pagination;
    let pHtml = `<span>Showing ${((p.current_page - 1) * p.per_page) + 1}-${Math.min(p.current_page * p.per_page, p.total)} of ${p.total}</span><div class="flex gap-2">`;
    if (p.current_page > 1) pHtml += `<button onclick="loadActivityLogs(${p.current_page - 1})" class="px-3 py-1 bg-gray-100 rounded hover:bg-gray-200 text-sm">Previous</button>`;
    if (p.current_page < p.total_pages) pHtml += `<button onclick="loadActivityLogs(${p.current_page + 1})" class="px-3 py-1 bg-gray-100 rounded hover:bg-gray-200 text-sm">Next</button>`;
    pHtml += '</div>';
    document.getElementById('logs-pagination').innerHTML = pHtml;
}

// ==================== MANAGE ADMINS ====================
async function loadAdmins() {
    const data = await API.get('/api/admin/manage-admins');
    const container = document.getElementById('admins-list');
    if (!data || !data.success || !data.data.length) {
        container.innerHTML = '<div class="text-center py-8 text-gray-400">No admin accounts found</div>';
        return;
    }
    container.innerHTML = `<div class="space-y-3">${data.data.map(a => {
        const pages = a.allowed_pages ? JSON.parse(a.allowed_pages) : [];
        const pagesHtml = a.role === 'complete' ? '<span class="text-xs text-green-600">Full Access</span>' :
            (pages.length ? pages.map(p => `<span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs mr-1 mb-1">${p}</span>`).join('') : '<span class="text-xs text-red-500">No pages assigned</span>');
        return `<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 bg-gray-50 rounded-xl border border-gray-100">
            <div>
                <div class="font-semibold text-gray-900">${escapeHtml(a.name)} ${a.id == 1 ? '<span class="badge badge-info text-xs ml-1">Super Admin</span>' : ''}</div>
                <div class="text-sm text-gray-500">${escapeHtml(a.email)} · <span class="badge ${a.status==='active'?'badge-success':'badge-danger'}">${a.status}</span> · <span class="badge ${a.role==='complete'?'badge-info':'badge-warning'}">${a.role}</span></div>
                <div class="mt-1">${pagesHtml}</div>
            </div>
            <div class="flex gap-2">${a.id != 1 ? `
                <button onclick="editAdmin(${a.id})" class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100"><i class="fas fa-edit"></i></button>
                <button onclick="deleteAdminUser(${a.id})" class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100"><i class="fas fa-trash"></i></button>` : ''}
            </div>
        </div>`;
    }).join('')}</div>`;
}

function showAddAdmin() {
    document.getElementById('admin-modal-title').textContent = 'Add Admin';
    document.getElementById('adm-id').value = '';
    document.getElementById('admin-form').reset();
    document.getElementById('pw-req').classList.remove('hidden');
    document.getElementById('adm-password').required = true;
    Modal.open('admin-modal');
}

async function editAdmin(id) {
    const data = await API.get('/api/admin/manage-admins');
    if (!data || !data.success) return;
    const admin = data.data.find(a => a.id == id);
    if (!admin) return;
    document.getElementById('admin-modal-title').textContent = 'Edit Admin';
    document.getElementById('adm-id').value = admin.id;
    document.getElementById('adm-name').value = admin.name;
    document.getElementById('adm-email').value = admin.email;
    document.getElementById('adm-role').value = admin.role || 'limited';
    document.getElementById('adm-password').value = '';
    document.getElementById('adm-password').required = false;
    document.getElementById('pw-req').classList.add('hidden');
    document.querySelectorAll('.adm-page').forEach(c => c.checked = false);
    if (admin.allowed_pages) {
        const pages = JSON.parse(admin.allowed_pages);
        pages.forEach(p => { const cb = document.querySelector(`.adm-page[value="${p}"]`); if (cb) cb.checked = true; });
    }
    Modal.open('admin-modal');
}

document.getElementById('admin-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('save-admin-btn');
    setLoading(btn, true);
    const id = document.getElementById('adm-id').value;
    const body = {
        name: document.getElementById('adm-name').value,
        email: document.getElementById('adm-email').value,
        password: document.getElementById('adm-password').value,
        role: document.getElementById('adm-role').value,
        allowed_pages: [...document.querySelectorAll('.adm-page:checked')].map(c => c.value)
    };
    const data = id ? await API.put(`/api/admin/manage-admins/${id}`, body) : await API.post('/api/admin/manage-admins', body);
    setLoading(btn, false);
    if (data && data.success) { Toast.success(data.message); Modal.close('admin-modal'); loadAdmins(); }
    else if (data) Toast.error(data.message);
});

async function deleteAdminUser(id) {
    if (!await confirmAction('Delete this admin?')) return;
    const data = await API.delete(`/api/admin/manage-admins/${id}`);
    if (data && data.success) { Toast.success(data.message); loadAdmins(); }
}

// ==================== CMS COURSES ====================
async function loadCmsCourses() {
    const data = await API.get('/api/admin/settings/cms');
    const container = document.getElementById('cms-courses-list');
    if (!data || !data.success || !data.data.length) {
        container.innerHTML = '<div class="text-center py-8 text-gray-400">No courses found. Add courses first in the Courses page.</div>';
        return;
    }
    container.innerHTML = data.data.map(c => `
        <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100 hover:bg-gray-100 transition cursor-pointer">
            <span class="font-medium text-gray-800">${escapeHtml(c.name)} <span class="text-gray-400 font-normal">(${escapeHtml(c.code)})</span></span>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" data-id="${c.id}" class="cms-display sr-only peer" ${c.display_on_landing == 1 ? 'checked' : ''}>
                <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
            </label>
        </label>
    `).join('');
}

async function saveCmsCourses() {
    const courses = [...document.querySelectorAll('.cms-display')].map(cb => ({
        id: parseInt(cb.dataset.id),
        display: cb.checked
    }));
    const data = await API.post('/api/admin/settings/cms', { courses });
    if (data && data.success) Toast.success(data.message);
    else if (data) Toast.error(data.message);
}

// ==================== SMTP SETTINGS ====================
async function loadSmtpSettings() {
    const data = await API.get('/api/admin/settings');
    if (!data || !data.success) return;
    const all = {};
    (data.raw || []).forEach(s => { all[s.setting_key] = s.setting_value; });
    ['contact_email','smtp_host','smtp_username','smtp_password','smtp_port','smtp_encryption'].forEach(key => {
        const el = document.getElementById('set-' + key);
        if (el) {
            el.value = all[key] || (key === 'smtp_port' ? '465' : key === 'contact_email' ? 'admin@agitacademy.com' : key === 'smtp_encryption' ? 'ssl' : '');
            if (key === 'smtp_password') {
                const setEl = document.getElementById('smtp-password-set');
                if (setEl) setEl.classList.toggle('hidden', !all[key] || all[key].length === 0);
            }
        }
    });
    const testEl = document.getElementById('test-email-to');
    if (testEl && !testEl.value) testEl.placeholder = all.contact_email || 'your@email.com';
}

async function saveSmtpSettings() {
    const settings = [
        { key: 'contact_email', value: document.getElementById('set-contact_email').value || 'admin@agitacademy.com', category: 'email' },
        { key: 'smtp_host', value: document.getElementById('set-smtp_host').value, category: 'email' },
        { key: 'smtp_username', value: document.getElementById('set-smtp_username').value, category: 'email' },
        { key: 'smtp_password', value: document.getElementById('set-smtp_password').value, category: 'email' },
        { key: 'smtp_port', value: document.getElementById('set-smtp_port').value || '465', category: 'email' },
        { key: 'smtp_encryption', value: document.getElementById('set-smtp_encryption').value || 'ssl', category: 'email' }
    ];
    const data = await API.post('/api/admin/settings', { settings });
    if (data && data.success) { Toast.success(data.message); document.getElementById('set-smtp_password').value = ''; loadSmtpSettings(); }
    else if (data) Toast.error(data.message);
}

async function sendTestEmail() {
    const to = document.getElementById('test-email-to').value.trim();
    if (!to) { Toast.error('Enter an email address to send the test to.'); return; }
    const btn = document.querySelector('button[onclick="sendTestEmail()"]');
    const orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
    const data = await API.post('/api/admin/settings/test-email', { email: to });
    btn.disabled = false;
    btn.innerHTML = orig;
    if (data && data.success) Toast.success(data.message);
    else if (data) Toast.error(data.message || 'Failed to send test email.');
}

// ==================== INIT ====================
document.addEventListener('DOMContentLoaded', () => {
    loadGeneralSettings();
    loadClassesForGrading();
    resetGradingForm();
});
</script>
