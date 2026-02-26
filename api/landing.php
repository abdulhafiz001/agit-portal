<?php
/**
 * Landing Page API - Public
 * Courses to display on landing page (CMS controlled), with topics
 */

function getLandingCourses() {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
    try {
        $db = getDB();
        $c = $db->query("SHOW COLUMNS FROM subjects LIKE 'display_on_landing'");
        $hasDisplay = $c && $c->fetch();
        $c = $db->query("SHOW COLUMNS FROM subjects LIKE 'image'");
        $hasImage = $c && $c->fetch();
        $c = $db->query("SHOW COLUMNS FROM subjects LIKE 'duration'");
        $hasDuration = $c && $c->fetch();
        $hasTopics = (bool) $db->query("SHOW TABLES LIKE 'course_topics'")->fetch();

        $select = 'id, name, code, description';
        if ($hasImage) $select .= ', image';
        if ($hasDuration) $select .= ', duration';

        $where = "status = 'active'";
        if ($hasDisplay) $where .= " AND (display_on_landing = 1 OR display_on_landing IS NULL)";

        $stmt = $db->query("SELECT {$select} FROM subjects WHERE {$where} ORDER BY name");
        $courses = ($stmt !== false) ? $stmt->fetchAll() : [];

        foreach ($courses as &$course) {
            if ($hasImage && !empty($course['image'])) {
                $course['image_url'] = rtrim(APP_URL, '/') . '/uploads/' . ltrim($course['image'], '/');
            } else {
                $course['image_url'] = null;
            }
            $course['topics'] = [];
            if ($hasTopics) {
                $t = $db->prepare("SELECT topic_title FROM course_topics WHERE subject_id = ? ORDER BY sort_order, id");
                $t->execute([$course['id']]);
                $course['topics'] = $t->fetchAll(PDO::FETCH_COLUMN);
            }
        }

        jsonResponse(['success' => true, 'data' => $courses]);
    } catch (Throwable $e) {
        jsonResponse(['success' => true, 'data' => []]);
    }
}
