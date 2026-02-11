<?php
/**
 * Admin Announcements API
 */

function getAnnouncements() {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT a.*, ad.name as author_name
        FROM announcements a
        LEFT JOIN admins ad ON ad.id = a.author_id
        ORDER BY a.created_at DESC
    ");
    $stmt->execute();
    jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
}

function createAnnouncement() {
    $db = getDB();
    $data = getPostData();
    $errors = validateRequired($data, ['title', 'content']);
    if ($errors) jsonResponse(['success' => false, 'message' => $errors[0]], 400);

    $stmt = $db->prepare("
        INSERT INTO announcements (title, content, author_id, target_audience, priority)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        sanitize($data['title']),
        $data['content'],
        $_SESSION['user_id'],
        $data['target_audience'] ?? 'all',
        $data['priority'] ?? 'normal'
    ]);
    logActivity('admin', $_SESSION['user_id'], 'create_announcement', 'Posted: ' . $data['title']);
    jsonResponse(['success' => true, 'message' => 'Announcement posted.']);
}

function updateAnnouncement($id) {
    $db = getDB();
    $data = getPostData();
    $stmt = $db->prepare("UPDATE announcements SET title=?, content=?, target_audience=?, priority=?, is_active=? WHERE id=?");
    $stmt->execute([
        sanitize($data['title']), $data['content'],
        $data['target_audience'] ?? 'all', $data['priority'] ?? 'normal',
        $data['is_active'] ?? 1, $id
    ]);
    jsonResponse(['success' => true, 'message' => 'Announcement updated.']);
}

function deleteAnnouncement($id) {
    $db = getDB();
    $db->prepare("DELETE FROM announcements WHERE id = ?")->execute([$id]);
    jsonResponse(['success' => true, 'message' => 'Announcement deleted.']);
}
