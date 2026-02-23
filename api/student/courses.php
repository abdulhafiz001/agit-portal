<?php
/**
 * Student Courses API
 */

function getMyCourses() {
    $db = getDB();
    $classId = $_SESSION['class_id'] ?? null;
    
    if (!$classId) {
        jsonResponse(['success' => true, 'data' => [], 'class_name' => 'No class assigned']);
        return;
    }
    
    // Get class info
    $classStmt = $db->prepare("SELECT name FROM classes WHERE id = ?");
    $classStmt->execute([$classId]);
    $className = $classStmt->fetch()['name'] ?? 'Unknown';
    
    $stmt = $db->prepare("
        SELECT s.id, s.name, s.code, s.description,
            GROUP_CONCAT(DISTINCT l.name SEPARATOR ', ') as lecturer_names
        FROM subjects s 
        INNER JOIN class_subjects cs ON cs.subject_id = s.id 
        LEFT JOIN lecturer_subjects ls ON ls.subject_id = s.id 
        LEFT JOIN lecturer_classes lc ON lc.lecturer_id = ls.lecturer_id AND lc.class_id = cs.class_id
        LEFT JOIN lecturers l ON l.id = ls.lecturer_id
        WHERE cs.class_id = ? AND s.status = 'active'
        GROUP BY s.id
        ORDER BY s.name
    ");
    $stmt->execute([$classId]);
    $courses = $stmt->fetchAll();
    
    $hasDuration = (bool) $db->query("SHOW COLUMNS FROM subjects LIKE 'duration'")->fetch();
    $hasImage = (bool) $db->query("SHOW COLUMNS FROM subjects LIKE 'image'")->fetch();
    $hasTopics = (bool) $db->query("SHOW TABLES LIKE 'course_topics'")->fetch();
    
    foreach ($courses as &$c) {
        if ($hasDuration || $hasImage) {
            $ext = $db->prepare("SELECT " . ($hasDuration ? "duration" : "NULL as duration") . ", " . ($hasImage ? "image" : "NULL as image") . " FROM subjects WHERE id = ?");
            $ext->execute([$c['id']]);
            $row = $ext->fetch();
            $c['duration'] = $row['duration'] ?? null;
            $c['image'] = $row['image'] ?? null;
        }
        $c['topics'] = [];
        if ($hasTopics) {
            try {
                $t = $db->prepare("SELECT topic_title FROM course_topics WHERE subject_id = ? ORDER BY sort_order, id");
                $t->execute([$c['id']]);
                $c['topics'] = $t->fetchAll(PDO::FETCH_COLUMN);
            } catch (Exception $e) {}
        }
    }
    
    jsonResponse([
        'success' => true, 
        'data' => $courses,
        'class_name' => $className
    ]);
}
