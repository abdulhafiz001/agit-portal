<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Approved - AGIT Academy</title>
    <link rel="icon" type="image/png" href="<?= APP_URL ?>/assets/images/agit-logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full text-center">
        <?php if (($approveResult['success'] ?? false)): ?>
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center">
                <i class="fas fa-check text-2xl text-green-600"></i>
            </div>
            <h1 class="text-xl font-bold text-gray-900 mb-2">Student Approved</h1>
            <p class="text-gray-600 text-sm mb-6"><?= htmlspecialchars($approveResult['message']) ?></p>
        <?php else: ?>
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-amber-100 flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-2xl text-amber-600"></i>
            </div>
            <h1 class="text-xl font-bold text-gray-900 mb-2">Unable to Process</h1>
            <p class="text-gray-600 text-sm mb-6"><?= htmlspecialchars($approveResult['message'] ?? 'Invalid or expired link.') ?></p>
        <?php endif; ?>
        <a href="<?= APP_URL ?>/admin/registrations" class="inline-block px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Go to Registrations</a>
    </div>
</body>
</html>
