<?php
/**
 * Common Announcements API for students/faculty
 */

function getMyAnnouncements() {
    $db = getDB();
    $role = $_SESSION['user_role'];
    $target = $role === 'student' ? 'students' : 'lecturers';
    $stmt = $db->prepare("
        SELECT a.*, ad.name as author_name
        FROM announcements a
        LEFT JOIN admins ad ON ad.id = a.author_id
        WHERE a.is_active = 1 AND (a.target_audience = 'all' OR a.target_audience = ?)
        ORDER BY a.priority = 'urgent' DESC, a.priority = 'important' DESC, a.created_at DESC
        LIMIT 20
    ");
    $stmt->execute([$target]);
    jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
}
