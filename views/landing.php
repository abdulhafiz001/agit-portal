<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - <?= APP_TAGLINE ?></title>
    <link rel="icon" type="image/png" href="<?= APP_URL ?>/assets/images/agit-logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/custom.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#eff6ff',100:'#dbeafe',200:'#bfdbfe',300:'#93c5fd',400:'#60a5fa',500:'#3b82f6',600:'#2563eb',700:'#1d4ed8',800:'#1e40af',900:'#1e3a8a' },
                    }
                }
            }
        }
    </script>
    <style>
        .hero-gradient { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #06b6d4 100%); }
        .float-animation { animation: float 6s ease-in-out infinite; }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-20px); } }
    </style>
</head>
<body class="bg-white font-sans antialiased">

    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 bg-white/90 backdrop-blur-md border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-3">
                    <img src="<?= APP_URL ?>/assets/images/agit-logo.png" alt="AGIT Logo" class="h-10 w-auto">
                    <span class="text-xl font-bold text-gray-900"><?= APP_NAME ?></span>
                </div>
                
                <!-- Desktop Nav -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="#features" class="text-gray-600 hover:text-primary-600 text-sm font-medium">Features</a>
                    <a href="#about" class="text-gray-600 hover:text-primary-600 text-sm font-medium">About</a>
                    <a href="#courses" class="text-gray-600 hover:text-primary-600 text-sm font-medium">Courses</a>
                    <a href="#portals" class="text-gray-600 hover:text-primary-600 text-sm font-medium">Portals</a>
                    <a href="#contact-form" class="text-gray-600 hover:text-primary-600 text-sm font-medium">Contact</a>
                </div>
                
                <div class="hidden md:flex items-center gap-3">
                    <a href="<?= APP_URL ?>/login/student" class="px-4 py-2 text-sm font-medium text-primary-600 border border-primary-600 rounded-lg hover:bg-primary-50 transition">Student Login</a>
                    <a href="<?= APP_URL ?>/login/faculty" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition">Faculty Login</a>
                </div>

                <!-- Mobile menu button -->
                <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="md:hidden p-2 text-gray-600">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t">
            <div class="px-4 py-4 space-y-3">
                <a href="#features" class="block text-gray-600 hover:text-primary-600 text-sm font-medium">Features</a>
                <a href="#about" class="block text-gray-600 hover:text-primary-600 text-sm font-medium">About</a>
                <a href="#courses" class="block text-gray-600 hover:text-primary-600 text-sm font-medium">Courses</a>
                <a href="#portals" class="block text-gray-600 hover:text-primary-600 text-sm font-medium">Portals</a>
                <a href="#contact-form" class="block text-gray-600 hover:text-primary-600 text-sm font-medium">Contact</a>
                <hr>
                <a href="<?= APP_URL ?>/login/student" class="block px-4 py-2 text-sm font-medium text-primary-600 border border-primary-600 rounded-lg text-center">Student Login</a>
                <a href="<?= APP_URL ?>/login/faculty" class="block px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg text-center">Faculty Login</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-gradient min-h-screen flex items-center pt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="text-white">
                    <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full px-4 py-2 mb-6">
                        <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                        <span class="text-sm font-medium">Academy Management System</span>
                    </div>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight mb-6">
                        Empowering <span class="text-cyan-300">Education</span> Through Technology
                    </h1>
                    <p class="text-lg text-blue-100 mb-8 max-w-xl">
                        A comprehensive platform for managing academic operations - from enrollment to examinations, 
                        grading to analytics. Everything your institution needs in one place.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="<?= APP_URL ?>/login/student" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-primary-700 font-semibold rounded-xl hover:bg-blue-50 transition shadow-lg">
                            <i class="fas fa-user-graduate"></i>
                            Student Portal
                        </a>
                        <a href="<?= APP_URL ?>/login/faculty" class="inline-flex items-center gap-2 px-6 py-3 bg-white/10 backdrop-blur-sm text-white font-semibold rounded-xl border border-white/30 hover:bg-white/20 transition">
                            <i class="fas fa-chalkboard-teacher"></i>
                            Faculty Portal
                        </a>
                    </div>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-6 mt-12 pt-8 border-t border-white/20">
                        <div>
                            <div class="text-2xl font-bold">500+</div>
                            <div class="text-blue-200 text-sm">Students</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold">50+</div>
                            <div class="text-blue-200 text-sm">Courses</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold">98%</div>
                            <div class="text-blue-200 text-sm">Success Rate</div>
                        </div>
                    </div>
                </div>
                
                <div class="hidden lg:flex justify-center">
                    <div class="relative float-animation">
                        <!-- Decorative elements -->
                        <div class="w-80 h-80 bg-white/10 backdrop-blur-sm rounded-3xl border border-white/20 p-8 relative">
                            <div class="absolute -top-6 -right-6 w-24 h-24 bg-cyan-400/20 rounded-2xl flex items-center justify-center">
                                <i class="fas fa-chart-line text-white text-3xl"></i>
                            </div>
                            <div class="absolute -bottom-4 -left-4 w-20 h-20 bg-emerald-400/20 rounded-2xl flex items-center justify-center">
                                <i class="fas fa-award text-white text-2xl"></i>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-book-open text-white"></i>
                                    </div>
                                    <div>
                                        <div class="text-white font-semibold text-sm">Course Management</div>
                                        <div class="text-blue-200 text-xs">Organize & deliver</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-laptop-code text-white"></i>
                                    </div>
                                    <div>
                                        <div class="text-white font-semibold text-sm">CBT Examinations</div>
                                        <div class="text-blue-200 text-xs">Secure & automated</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-chart-bar text-white"></i>
                                    </div>
                                    <div>
                                        <div class="text-white font-semibold text-sm">Analytics & Reports</div>
                                        <div class="text-blue-200 text-xs">Data-driven insights</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-users text-white"></i>
                                    </div>
                                    <div>
                                        <div class="text-white font-semibold text-sm">User Management</div>
                                        <div class="text-blue-200 text-xs">Role-based access</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">Features</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3">Everything You Need to Succeed</h2>
                <p class="text-gray-500 mt-4 max-w-2xl mx-auto">Our platform provides comprehensive tools for efficient academic management and student success.</p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition group">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-blue-600 transition">
                        <i class="fas fa-user-graduate text-blue-600 text-xl group-hover:text-white transition"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Student Management</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Complete student lifecycle management from enrollment to graduation with detailed profiles and tracking.</p>
                </div>
                
                <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition group">
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-600 transition">
                        <i class="fas fa-laptop-code text-emerald-600 text-xl group-hover:text-white transition"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">CBT Examinations</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Computer-based testing with multiple question types, auto-grading, timer, and anti-cheat measures.</p>
                </div>
                
                <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition group">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-purple-600 transition">
                        <i class="fas fa-chart-pie text-purple-600 text-xl group-hover:text-white transition"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Analytics & Reports</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Comprehensive analytics with visual charts for performance tracking, trends, and institutional insights.</p>
                </div>
                
                <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition group">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-amber-600 transition">
                        <i class="fas fa-chalkboard text-amber-600 text-xl group-hover:text-white transition"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Class Scheduling</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Flexible class management supporting semester-based and professional programs with smart scheduling.</p>
                </div>
                
                <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition group">
                    <div class="w-12 h-12 bg-rose-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-rose-600 transition">
                        <i class="fas fa-shield-alt text-rose-600 text-xl group-hover:text-white transition"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Secure & Reliable</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Role-based access control, encrypted passwords, SQL injection protection, and activity logging.</p>
                </div>
                
                <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition group">
                    <div class="w-12 h-12 bg-cyan-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-cyan-600 transition">
                        <i class="fas fa-mobile-alt text-cyan-600 text-xl group-hover:text-white transition"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Fully Responsive</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Access the platform from any device - desktop, tablet, or mobile with an optimized experience.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">About AGIT</span>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3 mb-6">Transforming Academic Excellence</h2>
                    <p class="text-gray-500 mb-6 leading-relaxed">
                        AGIT Academy is committed to providing world-class education through innovative technology solutions. 
                        Our management system streamlines every aspect of academic operations, allowing educators to focus on 
                        what matters most - teaching.
                    </p>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-check text-emerald-600 text-xs"></i>
                            </div>
                            <p class="text-gray-600 text-sm">Automated grading and promotion systems save hundreds of administrative hours</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-check text-emerald-600 text-xs"></i>
                            </div>
                            <p class="text-gray-600 text-sm">Real-time analytics provide actionable insights for data-driven decisions</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-check text-emerald-600 text-xs"></i>
                            </div>
                            <p class="text-gray-600 text-sm">Secure CBT platform ensures exam integrity and fair assessment</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-primary-50 to-cyan-50 rounded-3xl p-8">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="bg-white rounded-2xl p-6 shadow-sm text-center">
                            <div class="text-3xl font-bold text-primary-600">12+</div>
                            <div class="text-gray-500 text-sm mt-1">Programs</div>
                        </div>
                        <div class="bg-white rounded-2xl p-6 shadow-sm text-center">
                            <div class="text-3xl font-bold text-emerald-600">30+</div>
                            <div class="text-gray-500 text-sm mt-1">Faculty</div>
                        </div>
                        <div class="bg-white rounded-2xl p-6 shadow-sm text-center">
                            <div class="text-3xl font-bold text-amber-600">500+</div>
                            <div class="text-gray-500 text-sm mt-1">Students</div>
                        </div>
                        <div class="bg-white rounded-2xl p-6 shadow-sm text-center">
                            <div class="text-3xl font-bold text-purple-600">95%</div>
                            <div class="text-gray-500 text-sm mt-1">Pass Rate</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Courses Section (CMS controlled - server-side rendered) -->
    <section id="courses" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">Our Courses</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3">Explore Our Programs</h2>
                <p class="text-gray-500 mt-4 max-w-2xl mx-auto">Discover the courses we offer. Contact us for enrollment enquiries.</p>
            </div>
            <div id="landing-courses" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php
                $landingCourses = [];
                try {
                    $db = getDB();
                    $c = $db->query("SHOW COLUMNS FROM subjects LIKE 'display_on_landing'");
                    $hasDisplay = $c && $c->fetch();
                    $c = $db->query("SHOW COLUMNS FROM subjects LIKE 'image'");
                    $hasImage = $c && $c->fetch();
                    $c = $db->query("SHOW COLUMNS FROM subjects LIKE 'duration'");
                    $hasDuration = $c && $c->fetch();
                    $sel = 'id, name, code, description';
                    if ($hasImage) $sel .= ', image';
                    if ($hasDuration) $sel .= ', duration';
                    $where = "status = 'active'";
                    if ($hasDisplay) $where .= " AND (display_on_landing = 1 OR display_on_landing IS NULL)";
                    $stmt = $db->query("SELECT {$sel} FROM subjects WHERE {$where} ORDER BY name");
                    $landingCourses = ($stmt !== false) ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
                } catch (Throwable $e) { $landingCourses = []; }
                if (empty($landingCourses)): ?>
                <div class="col-span-full text-center py-12 text-gray-400">No courses to display. Admin can configure in Settings &gt; Landing Page CMS.</div>
                <?php else:
                foreach ($landingCourses as $c):
                    $img = !empty($c['image']) ? '<img src="' . htmlspecialchars(APP_URL . '/uploads/' . $c['image']) . '" alt="' . htmlspecialchars($c['name'] ?? '') . '" class="w-full h-40 object-cover rounded-t-xl">' : '';
                    $dur = !empty($c['duration']) ? '<p class="text-xs text-blue-600 mt-2"><i class="fas fa-clock mr-1"></i>' . htmlspecialchars($c['duration']) . '</p>' : '';
                    $desc = !empty($c['description']) ? '<p class="text-sm text-gray-600 mt-2 line-clamp-2">' . htmlspecialchars($c['description']) . '</p>' : '';
                ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition">
                    <?= $img ?>
                    <div class="p-5">
                        <h3 class="font-semibold text-gray-900 text-lg"><?= htmlspecialchars($c['name'] ?? '') ?></h3>
                        <p class="text-sm text-gray-500 font-mono mt-1"><?= htmlspecialchars($c['code'] ?? '') ?></p>
                        <?= $dur ?>
                        <?= $desc ?>
                    </div>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section id="contact-form" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mx-auto">
                <div class="text-center mb-12">
                    <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">Get in Touch</span>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3">Contact Us</h2>
                    <p class="text-gray-500 mt-4">Have a question? Fill out the form below and we'll get back to you.</p>
                </div>
                <form id="contact-form-el" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Your Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm" placeholder="John Doe">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm" placeholder="you@example.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Subject</label>
                        <input type="text" name="subject" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm" placeholder="Enquiry about...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Message <span class="text-red-500">*</span></label>
                        <textarea name="message" required rows="5" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm resize-none" placeholder="Your message..."></textarea>
                    </div>
                    <button type="submit" id="contact-submit-btn" class="w-full py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 focus:ring-4 focus:ring-blue-500/30 transition text-sm">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Portal Access Section -->
    <section id="portals" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">Access Portals</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3">Choose Your Portal</h2>
                <p class="text-gray-500 mt-4">Select your role to access the appropriate portal.</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition text-center group border-2 border-transparent hover:border-blue-500">
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-600 transition">
                        <i class="fas fa-user-graduate text-blue-600 text-2xl group-hover:text-white transition"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Student Portal</h3>
                    <p class="text-gray-500 text-sm mb-4">Access courses, take exams, view results and track your progress.</p>
                    <a href="<?= APP_URL ?>/login/student" class="text-primary-600 text-sm font-semibold hover:underline">Login <i class="fas fa-arrow-right ml-1"></i></a>
                    <?php
                    $db = getDB();
                    $regOn = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'allow_registration'")->fetchColumn();
                    if ($regOn === '1' || $regOn === 'enabled'): ?>
                    <span class="mx-2 text-gray-300">|</span>
                    <a href="<?= APP_URL ?>/register/student" class="text-blue-500 text-sm font-semibold hover:underline">Register <i class="fas fa-user-plus ml-1"></i></a>
                    <?php endif; ?>
                </div>
                
                <a href="<?= APP_URL ?>/login/faculty" class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition text-center group border-2 border-transparent hover:border-emerald-500">
                    <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-emerald-600 transition">
                        <i class="fas fa-chalkboard-teacher text-emerald-600 text-2xl group-hover:text-white transition"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Faculty Portal</h3>
                    <p class="text-gray-500 text-sm mb-4">Manage classes, create exams, grade students and upload materials.</p>
                    <span class="text-emerald-600 text-sm font-semibold group-hover:underline">Login <i class="fas fa-arrow-right ml-1"></i></span>
                </a>
                
                <a href="<?= APP_URL ?>/login/admin" class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition text-center group border-2 border-transparent hover:border-purple-500">
                    <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-purple-600 transition">
                        <i class="fas fa-user-shield text-purple-600 text-2xl group-hover:text-white transition"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Admin Portal</h3>
                    <p class="text-gray-500 text-sm mb-4">Full system control - manage users, courses, exams and analytics.</p>
                    <span class="text-purple-600 text-sm font-semibold group-hover:underline">Login <i class="fas fa-arrow-right ml-1"></i></span>
                </a>
            </div>
        </div>
    </section>

    <!-- Contact / Footer -->
    <section id="contact" class="py-16 bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="<?= APP_URL ?>/assets/images/agit-logo.png" alt="AGIT Logo" class="h-10 w-auto brightness-0 invert">
                        <span class="text-xl font-bold"><?= APP_NAME ?></span>
                    </div>
                    <p class="text-gray-400 text-sm max-w-md leading-relaxed">
                        Empowering educational institutions with modern technology solutions for seamless academic management and student success.
                    </p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#features" class="hover:text-white transition">Features</a></li>
                        <li><a href="#about" class="hover:text-white transition">About</a></li>
                        <li><a href="#portals" class="hover:text-white transition">Portals</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Contact</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><i class="fas fa-envelope mr-2"></i> info@agit.edu</li>
                        <li><i class="fas fa-phone mr-2"></i> +234 000 000 0000</li>
                        <li><i class="fas fa-map-marker-alt mr-2"></i> AGIT Academy Campus</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-500 text-sm">
                &copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved. | Powered by AAMS v<?= APP_VERSION ?>
            </div>
        </div>
    </section>

    <script>window.APP_URL = <?= json_encode(APP_URL) ?>;</script>
    <script src="<?= APP_URL ?>/assets/js/app.js"></script>
    <script>
        const APP_URL = window.APP_URL || <?= json_encode(APP_URL) ?>;

        // Contact form submit
        document.getElementById('contact-form-el').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('contact-submit-btn');
            const origText = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Sending...';
            const form = e.target;
            const body = {
                name: form.name.value,
                email: form.email.value,
                subject: form.subject.value || 'AGIT Solutions Enquiry',
                message: form.message.value
            };
            try {
                const res = await fetch(APP_URL + '/api/contact', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                });
                const data = await res.json();
                if (data.success) {
                    if (typeof Toast !== 'undefined') Toast.success(data.message);
                    else alert(data.message);
                    form.reset();
                } else {
                    if (typeof Toast !== 'undefined') Toast.error(data.message);
                    else alert(data.message || 'Failed to send.');
                }
            } catch (err) {
                if (typeof Toast !== 'undefined') Toast.error('Network error. Please try again.');
                else alert('Network error. Please try again.');
            }
            btn.disabled = false;
            btn.textContent = origText;
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    document.getElementById('mobile-menu')?.classList.add('hidden');
                }
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('nav');
            if (window.scrollY > 50) {
                nav.classList.add('shadow-md');
            } else {
                nav.classList.remove('shadow-md');
            }
        });
    </script>
</body>
</html>
