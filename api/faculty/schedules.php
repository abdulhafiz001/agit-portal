<?php
/**
 * Faculty Schedules API
 */

function getMySchedules() {
    $db = getDB();
    $lecturerId = $_SESSION['user_id'];
    $stmt = $db->prepare("SELECT cs.*, c.name as class_name, s.name as subject_name, s.code as subject_code
        FROM class_schedules cs
        JOIN classes c ON c.id = cs.class_id
        JOIN subjects s ON s.id = cs.subject_id
        WHERE cs.lecturer_id = ? AND cs.status = 'active'
        ORDER BY FIELD(cs.day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday'), cs.start_time");
    $stmt->execute([$lecturerId]);
    jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
}

function createMySchedule() {
    $db = getDB();
    $data = getPostData();
    $errors = validateRequired($data, ['class_id', 'subject_id', 'day_of_week', 'start_time', 'end_time']);
    if ($errors) jsonResponse(['success' => false, 'message' => $errors[0]], 400);
    
    $stmt = $db->prepare("INSERT INTO class_schedules (class_id, subject_id, lecturer_id, day_of_week, start_time, end_time, room, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$data['class_id'], $data['subject_id'], $_SESSION['user_id'], $data['day_of_week'], $data['start_time'], $data['end_time'], $data['room'] ?? '', $data['notes'] ?? '']);
    jsonResponse(['success' => true, 'message' => 'Schedule created.']);
}
