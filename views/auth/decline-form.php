<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Decline Application - AGIT Academy</title>
    <link rel="icon" type="image/png" href="<?= APP_URL ?>/assets/images/agit-logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full">
        <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
            <i class="fas fa-times text-xl text-red-600"></i>
        </div>
        <h1 class="text-xl font-bold text-gray-900 mb-2 text-center">Decline Application</h1>
        <p class="text-gray-600 text-sm mb-6 text-center">Please provide a reason for rejecting this application. The student will receive this in an email.</p>
        <form method="post" action="<?= APP_URL ?>/decline-student">
            <input type="hidden" name="token" value="<?= htmlspecialchars($declineToken ?? '') ?>">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason for rejection <span class="text-red-500">*</span></label>
                <textarea name="reason" required minlength="10" rows="4" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="e.g. Class is full for this intake..."></textarea>
                <p class="text-xs text-gray-500 mt-1">Minimum 10 characters</p>
            </div>
            <div class="flex gap-3">
                <a href="<?= APP_URL ?>/admin/registrations" class="flex-1 py-2.5 border border-gray-200 text-gray-700 rounded-lg text-sm font-medium text-center hover:bg-gray-50">Cancel</a>
                <button type="submit" class="flex-1 py-2.5 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700">Decline & Notify Student</button>
            </div>
        </form>
    </div>
</body>
</html>
