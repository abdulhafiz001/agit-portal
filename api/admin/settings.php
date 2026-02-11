<?php
/**
 * Admin Settings API
 */

function getSettings() {
    $db = getDB();
    $category = $_GET['category'] ?? '';
    $where = $category ? "WHERE category = ?" : "";
    $params = $category ? [$category] : [];
    $stmt = $db->prepare("SELECT * FROM settings $where ORDER BY category, setting_key");
    $stmt->execute($params);
    $settings = $stmt->fetchAll();
    // Convert to key-value map grouped by category
    $grouped = [];
    foreach ($settings as $s) {
        $grouped[$s['category']][$s['setting_key']] = $s['setting_value'];
    }
    jsonResponse(['success' => true, 'data' => $grouped, 'raw' => $settings]);
}

function updateSettings() {
    $db = getDB();
    $data = getPostData();
    $settings = $data['settings'] ?? [];
    if (empty($settings)) jsonResponse(['success' => false, 'message' => 'No settings provided.'], 400);

    $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value, category) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    foreach ($settings as $item) {
        $stmt->execute([$item['key'], $item['value'], $item['category'] ?? 'general']);
    }
    logActivity('admin', $_SESSION['user_id'], 'update_settings', 'Updated portal settings');
    jsonResponse(['success' => true, 'message' => 'Settings saved successfully.']);
}

// Grading Configuration
function getGradingConfigs() {
    $db = getDB();
    // Get all config groups
    $groups = $db->query("SELECT DISTINCT config_group FROM grading_configs ORDER BY config_group")->fetchAll(PDO::FETCH_COLUMN);
    $result = [];
    foreach ($groups as $g) {
        $grades = $db->prepare("SELECT * FROM grading_configs WHERE config_group = ? ORDER BY min_score DESC");
        $grades->execute([$g]);
        $classes = $db->prepare("SELECT gc.class_id, c.name as class_name FROM grading_config_classes gc JOIN classes c ON c.id = gc.class_id WHERE gc.config_group = ?");
        $classes->execute([$g]);
        $result[] = ['group' => $g, 'grades' => $grades->fetchAll(), 'classes' => $classes->fetchAll()];
    }
    jsonResponse(['success' => true, 'data' => $result]);
}

function saveGradingConfig() {
    $db = getDB();
    $data = getPostData();
    $group = $data['config_group'] ?? null;
    $grades = $data['grades'] ?? [];
    $classIds = $data['class_ids'] ?? [];

    if (empty($grades)) jsonResponse(['success' => false, 'message' => 'At least one grade is required.'], 400);

    // Auto-assign group number if new
    if (!$group) {
        $max = $db->query("SELECT COALESCE(MAX(config_group),0) FROM grading_configs")->fetchColumn();
        $group = $max + 1;
    }

    // Clear old grades for this group
    $db->prepare("DELETE FROM grading_configs WHERE config_group = ?")->execute([$group]);
    $db->prepare("DELETE FROM grading_config_classes WHERE config_group = ?")->execute([$group]);

    // Insert grades
    $stmt = $db->prepare("INSERT INTO grading_configs (name, min_score, max_score, grade, remark, config_group) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($grades as $g) {
        $stmt->execute([$g['name'] ?? $g['grade'], $g['min_score'], $g['max_score'], $g['grade'], $g['remark'] ?? '', $group]);
    }

    // Assign classes
    if (!empty($classIds)) {
        $clsStmt = $db->prepare("INSERT INTO grading_config_classes (config_group, class_id) VALUES (?, ?)");
        foreach ($classIds as $cid) { $clsStmt->execute([$group, $cid]); }
    }

    logActivity('admin', $_SESSION['user_id'], 'save_grading', "Saved grading config group #$group");
    jsonResponse(['success' => true, 'message' => 'Grading configuration saved.', 'config_group' => $group]);
}

function deleteGradingConfig($group) {
    $db = getDB();
    $db->prepare("DELETE FROM grading_configs WHERE config_group = ?")->execute([$group]);
    $db->prepare("DELETE FROM grading_config_classes WHERE config_group = ?")->execute([$group]);
    jsonResponse(['success' => true, 'message' => 'Grading configuration deleted.']);
}

// Promotion Rules
function getPromotionRules() {
    $db = getDB();
    $stmt = $db->prepare("SELECT pr.*, c.name as class_name FROM promotion_rules pr LEFT JOIN classes c ON c.id = pr.class_id ORDER BY pr.created_at DESC");
    $stmt->execute();
    jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
}

function savePromotionRule() {
    $db = getDB();
    $data = getPostData();
    $errors = validateRequired($data, ['name', 'rule_type', 'rule_value']);
    if ($errors) jsonResponse(['success' => false, 'message' => $errors[0]], 400);

    $id = $data['id'] ?? null;
    if ($id) {
        $db->prepare("UPDATE promotion_rules SET name=?, rule_type=?, rule_value=?, class_id=?, status=? WHERE id=?")->execute([
            sanitize($data['name']), $data['rule_type'], $data['rule_value'], $data['class_id'] ?: null, $data['status'] ?? 'active', $id
        ]);
    } else {
        $db->prepare("INSERT INTO promotion_rules (name, rule_type, rule_value, class_id) VALUES (?, ?, ?, ?)")->execute([
            sanitize($data['name']), $data['rule_type'], $data['rule_value'], $data['class_id'] ?: null
        ]);
    }
    logActivity('admin', $_SESSION['user_id'], 'save_promotion_rule', 'Saved promotion rule: ' . $data['name']);
    jsonResponse(['success' => true, 'message' => 'Promotion rule saved.']);
}

function deletePromotionRule($id) {
    $db = getDB();
    $db->prepare("DELETE FROM promotion_rules WHERE id = ?")->execute([$id]);
    jsonResponse(['success' => true, 'message' => 'Promotion rule deleted.']);
}

// Process promotions
function processPromotions() {
    $db = getDB();
    $data = getPostData();
    $classId = $data['class_id'] ?? '';
    $semester = $data['semester'] ?? '';
    $targetClassId = $data['target_class_id'] ?? '';
    if (!$classId || !$targetClassId) jsonResponse(['success' => false, 'message' => 'Source and target class required.'], 400);

    // Get active rules for this class
    $rules = $db->prepare("SELECT * FROM promotion_rules WHERE status = 'active' AND (class_id IS NULL OR class_id = ?)");
    $rules->execute([$classId]);
    $ruleList = $rules->fetchAll();

    // Get students in class with their scores
    $students = $db->prepare("SELECT s.id, s.name, s.matric_no, COALESCE(AVG(sc.total), 0) as avg_score, COUNT(DISTINCT sc.subject_id) as subjects_scored, SUM(CASE WHEN sc.total >= 50 THEN 1 ELSE 0 END) as passed_subjects FROM students s LEFT JOIN scores sc ON sc.student_id = s.id AND sc.class_id = ? " . ($semester ? "AND sc.semester = ?" : "") . " WHERE s.class_id = ? AND s.status = 'active' GROUP BY s.id");
    $params = $semester ? [$classId, $semester, $classId] : [$classId, $classId];
    $students->execute($params);
    $studentList = $students->fetchAll();

    $promoted = 0; $retained = 0; $details = [];
    foreach ($studentList as $st) {
        $pass = true;
        foreach ($ruleList as $rule) {
            if ($rule['rule_type'] === 'min_average' && $st['avg_score'] < $rule['rule_value']) $pass = false;
            if ($rule['rule_type'] === 'min_pass_subjects' && $st['passed_subjects'] < $rule['rule_value']) $pass = false;
        }
        $details[] = ['id' => $st['id'], 'name' => $st['name'], 'matric_no' => $st['matric_no'], 'avg_score' => round($st['avg_score'], 2), 'passed_subjects' => $st['passed_subjects'], 'promoted' => $pass];
        if ($pass) {
            $db->prepare("UPDATE students SET class_id = ? WHERE id = ?")->execute([$targetClassId, $st['id']]);
            $promoted++;
        } else { $retained++; }
    }
    logActivity('admin', $_SESSION['user_id'], 'process_promotions', "Promoted $promoted, retained $retained from class #$classId");
    jsonResponse(['success' => true, 'message' => "$promoted students promoted, $retained retained.", 'promoted' => $promoted, 'retained' => $retained, 'details' => $details]);
}

// Activity Logs
function getActivityLogs() {
    $db = getDB();
    $type = $_GET['type'] ?? '';
    $page = max(1, (int)($_GET['page'] ?? 1));
    $where = []; $params = [];
    if ($type) { $where[] = "user_type = ?"; $params[] = $type; }
    $wc = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $total = $db->prepare("SELECT COUNT(*) FROM activity_logs $wc");
    $total->execute($params);
    $pagination = paginate($total->fetchColumn(), $page, 25);

    $stmt = $db->prepare("SELECT * FROM activity_logs $wc ORDER BY created_at DESC LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}");
    $stmt->execute($params);
    jsonResponse(['success' => true, 'data' => $stmt->fetchAll(), 'pagination' => $pagination]);
}
