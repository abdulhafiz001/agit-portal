<?php
/**
 * Faculty - My Assigned Courses
 */

function getMyCourses() {
    $db = getDB();
    $lecturerId = $_SESSION['user_id'];
    
    $cols = ['s.id', 's.name', 's.code', 's.description'];
    if ((bool) $db->query("SHOW COLUMNS FROM subjects LIKE 'duration'")->fetch()) $cols[] = 's.duration';
    else $cols[] = 'NULL as duration';
    if ((bool) $db->query("SHOW COLUMNS FROM subjects LIKE 'image'")->fetch()) $cols[] = 's.image';
    else $cols[] = 'NULL as image';
    
    $select = implode(', ', $cols);
    $stmt = $db->prepare("
        SELECT {$select},
            (SELECT GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') FROM lecturer_classes lc JOIN classes c ON c.id = lc.class_id WHERE lc.lecturer_id = ? AND EXISTS (SELECT 1 FROM class_subjects cs WHERE cs.class_id = c.id AND cs.subject_id = s.id)) as class_names
        FROM subjects s 
        INNER JOIN lecturer_subjects ls ON ls.subject_id = s.id 
        WHERE ls.lecturer_id = ? AND s.status = 'active'
        ORDER BY s.name
    ");
    $stmt->execute([$lecturerId, $lecturerId]);
    $courses = $stmt->fetchAll();
    
    $hasTopics = (bool) $db->query("SHOW TABLES LIKE 'course_topics'")->fetch();
    foreach ($courses as &$c) {
        $c['topics'] = [];
        if ($hasTopics) {
            try {
                $t = $db->prepare("SELECT id, topic_title, sort_order FROM course_topics WHERE subject_id = ? ORDER BY sort_order, id");
                $t->execute([$c['id']]);
                $c['topics'] = $t->fetchAll();
            } catch (Exception $e) {}
        }
    }
    
    jsonResponse(['success' => true, 'data' => $courses]);
}
