<?php
/**
 * Landing Page API - Public
 * Courses to display on landing page (CMS controlled)
 */

function getLandingCourses() {
    try {
        $db = getDB();
        $c = $db->query("SHOW COLUMNS FROM subjects LIKE 'display_on_landing'");
        $hasDisplay = $c && $c->fetch();
        $c = $db->query("SHOW COLUMNS FROM subjects LIKE 'image'");
        $hasImage = $c && $c->fetch();
        $c = $db->query("SHOW COLUMNS FROM subjects LIKE 'duration'");
        $hasDuration = $c && $c->fetch();
        
        $select = 'id, name, code, description';
        if ($hasImage) $select .= ', image';
        if ($hasDuration) $select .= ', duration';
        
        $where = "status = 'active'";
        if ($hasDisplay) $where .= " AND (display_on_landing = 1 OR display_on_landing IS NULL)";
        
        $stmt = $db->query("SELECT {$select} FROM subjects WHERE {$where} ORDER BY name");
        $courses = ($stmt !== false) ? $stmt->fetchAll() : [];
        jsonResponse(['success' => true, 'data' => $courses]);
    } catch (Throwable $e) {
        jsonResponse(['success' => true, 'data' => []]);
    }
}
