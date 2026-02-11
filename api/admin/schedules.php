<?php
/**
 * Class Schedules API
 */

function getSchedules() {
    $db = getDB();
    $classId = $_GET['class_id'] ?? '';
    $lecturerId = $_GET['lecturer_id'] ?? '';
    $where = []; $params = [];
    if ($classId) { $where[] = "cs.class_id = ?"; $params[] = $classId; }
    if ($lecturerId) { $where[] = "cs.lecturer_id = ?"; $params[] = $lecturerId; }
    $wc = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    
    $stmt = $db->prepare("SELECT cs.*, c.name as class_name, s.name as subject_name, s.code as subject_code, l.name as lecturer_name
        FROM class_schedules cs
        JOIN classes c ON c.id = cs.class_id
        JOIN subjects s ON s.id = cs.subject_id
        JOIN lecturers l ON l.id = cs.lecturer_id
        $wc ORDER BY FIELD(cs.day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday'), cs.start_time");
    $stmt->execute($params);
    jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
}

function createSchedule() {
    $db = getDB();
    $data = getPostData();
    $errors = validateRequired($data, ['class_id', 'subject_id', 'lecturer_id', 'day_of_week', 'start_time', 'end_time']);
    if ($errors) jsonResponse(['success' => false, 'message' => $errors[0]], 400);
    
    $stmt = $db->prepare("INSERT INTO class_schedules (class_id, subject_id, lecturer_id, day_of_week, start_time, end_time, room, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$data['class_id'], $data['subject_id'], $data['lecturer_id'], $data['day_of_week'], $data['start_time'], $data['end_time'], $data['room'] ?? '', $data['notes'] ?? '']);
    logActivity('admin', $_SESSION['user_id'], 'create_schedule', 'Created class schedule');
    jsonResponse(['success' => true, 'message' => 'Schedule created.']);
}

function updateSchedule($id) {
    $db = getDB();
    $data = getPostData();
    $stmt = $db->prepare("UPDATE class_schedules SET class_id=?, subject_id=?, lecturer_id=?, day_of_week=?, start_time=?, end_time=?, room=?, notes=?, status=? WHERE id=?");
    $stmt->execute([$data['class_id'], $data['subject_id'], $data['lecturer_id'], $data['day_of_week'], $data['start_time'], $data['end_time'], $data['room'] ?? '', $data['notes'] ?? '', $data['status'] ?? 'active', $id]);
    jsonResponse(['success' => true, 'message' => 'Schedule updated.']);
}

function deleteSchedule($id) {
    $db = getDB();
    $db->prepare("DELETE FROM class_schedules WHERE id = ?")->execute([$id]);
    jsonResponse(['success' => true, 'message' => 'Schedule deleted.']);
}
