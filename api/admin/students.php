<?php
/**
 * Admin Students API
 */

function getStudents() {
    $db = getDB();
    $search = $_GET['search'] ?? '';
    $classId = $_GET['class_id'] ?? '';
    $status = $_GET['status'] ?? '';
    $page = max(1, (int)($_GET['page'] ?? 1));
    
    $where = [];
    $params = [];
    
    if ($search) {
        $where[] = "(s.name LIKE ? OR s.email LIKE ? OR s.matric_no LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    if ($classId) {
        $where[] = "s.class_id = ?";
        $params[] = $classId;
    }
    if ($status) {
        $where[] = "s.status = ?";
        $params[] = $status;
    }
    
    $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    
    // Count
    $countStmt = $db->prepare("SELECT COUNT(*) as total FROM students s $whereClause");
    $countStmt->execute($params);
    $total = $countStmt->fetch()['total'];
    $pagination = paginate($total, $page);
    
    // Fetch
    $stmt = $db->prepare("
        SELECT s.*, c.name as class_name 
        FROM students s 
        LEFT JOIN classes c ON c.id = s.class_id 
        $whereClause 
        ORDER BY s.created_at DESC 
        LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}
    ");
    $stmt->execute($params);
    $students = $stmt->fetchAll();
    
    // Remove password from response
    foreach ($students as &$s) { unset($s['password']); }
    
    jsonResponse(['success' => true, 'data' => $students, 'pagination' => $pagination]);
}

function getStudent($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT s.*, c.name as class_name FROM students s LEFT JOIN classes c ON c.id = s.class_id WHERE s.id = ?");
    $stmt->execute([$id]);
    $student = $stmt->fetch();
    if (!$student) jsonResponse(['success' => false, 'message' => 'Student not found.'], 404);
    unset($student['password']);
    jsonResponse(['success' => true, 'data' => $student]);
}

function createStudent() {
    $db = getDB();
    $data = getPostData();
    
    $errors = validateRequired($data, ['name', 'email', 'matric_no']);
    if ($errors) jsonResponse(['success' => false, 'message' => $errors[0]], 400);
    if (!isValidEmail($data['email'])) jsonResponse(['success' => false, 'message' => 'Invalid email format.'], 400);
    
    // Check unique
    $stmt = $db->prepare("SELECT id FROM students WHERE email = ? OR matric_no = ?");
    $stmt->execute([$data['email'], $data['matric_no']]);
    if ($stmt->fetch()) jsonResponse(['success' => false, 'message' => 'Email or Matric No already exists.'], 400);
    
    $password = hashPassword($data['password'] ?? 'password');
    
    $stmt = $db->prepare("
        INSERT INTO students (name, email, matric_no, class_id, password, phone, gender, date_of_birth, address, guardian_name, guardian_phone, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')
    ");
    $stmt->execute([
        sanitize($data['name']),
        sanitize($data['email']),
        sanitize($data['matric_no']),
        $data['class_id'] ?: null,
        $password,
        sanitize($data['phone'] ?? ''),
        $data['gender'] ?? null,
        $data['date_of_birth'] ?? null,
        sanitize($data['address'] ?? ''),
        sanitize($data['guardian_name'] ?? ''),
        sanitize($data['guardian_phone'] ?? ''),
    ]);
    
    logActivity('admin', $_SESSION['user_id'], 'create_student', 'Created student: ' . $data['name']);
    jsonResponse(['success' => true, 'message' => 'Student created successfully.']);
}

function updateStudent($id) {
    $db = getDB();
    $data = getPostData();
    
    if (!$id) jsonResponse(['success' => false, 'message' => 'Student ID required.'], 400);
    
    $errors = validateRequired($data, ['name', 'email', 'matric_no']);
    if ($errors) jsonResponse(['success' => false, 'message' => $errors[0]], 400);
    
    // Check unique (excluding current)
    $stmt = $db->prepare("SELECT id FROM students WHERE (email = ? OR matric_no = ?) AND id != ?");
    $stmt->execute([$data['email'], $data['matric_no'], $id]);
    if ($stmt->fetch()) jsonResponse(['success' => false, 'message' => 'Email or Matric No already exists.'], 400);
    
    $sql = "UPDATE students SET name=?, email=?, matric_no=?, class_id=?, phone=?, gender=?, date_of_birth=?, address=?, guardian_name=?, guardian_phone=?, status=?";
    $params = [
        sanitize($data['name']),
        sanitize($data['email']),
        sanitize($data['matric_no']),
        $data['class_id'] ?: null,
        sanitize($data['phone'] ?? ''),
        $data['gender'] ?? null,
        $data['date_of_birth'] ?? null,
        sanitize($data['address'] ?? ''),
        sanitize($data['guardian_name'] ?? ''),
        sanitize($data['guardian_phone'] ?? ''),
        $data['status'] ?? 'active',
    ];
    
    if (!empty($data['password'])) {
        $sql .= ", password=?";
        $params[] = hashPassword($data['password']);
    }
    
    $sql .= " WHERE id=?";
    $params[] = $id;
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    
    logActivity('admin', $_SESSION['user_id'], 'update_student', 'Updated student ID: ' . $id);
    jsonResponse(['success' => true, 'message' => 'Student updated successfully.']);
}

function deleteStudent($id) {
    $db = getDB();
    if (!$id) jsonResponse(['success' => false, 'message' => 'Student ID required.'], 400);
    
    $stmt = $db->prepare("DELETE FROM students WHERE id = ?");
    $stmt->execute([$id]);
    
    logActivity('admin', $_SESSION['user_id'], 'delete_student', 'Deleted student ID: ' . $id);
    jsonResponse(['success' => true, 'message' => 'Student deleted successfully.']);
}

function importStudents() {
    if (!isset($_FILES['csv_file'])) {
        jsonResponse(['success' => false, 'message' => 'No file uploaded.'], 400);
    }
    
    $file = $_FILES['csv_file'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['csv', 'xls', 'xlsx'])) {
        jsonResponse(['success' => false, 'message' => 'Only CSV, XLS, or XLSX files are allowed.'], 400);
    }
    
    $handle = fopen($file['tmp_name'], 'r');
    if (!$handle) {
        jsonResponse(['success' => false, 'message' => 'Could not read the uploaded file.'], 400);
    }
    
    $header = fgetcsv($handle); // Skip header row
    
    $db = getDB();
    $imported = 0;
    $errors = [];
    $row = 1;
    
    // Get class_id and subject_ids from form
    $formClassId = $_POST['class_id'] ?? null;
    $subjectIds = json_decode($_POST['subject_ids'] ?? '[]', true);
    
    while (($line = fgetcsv($handle)) !== false) {
        $row++;
        
        // Skip empty rows
        if (empty(array_filter($line))) continue;
        
        // Only Name, Email, Matric No are required (columns 0, 1, 2)
        $name = isset($line[0]) ? trim($line[0]) : '';
        $email = isset($line[1]) ? trim($line[1]) : '';
        $matric = isset($line[2]) ? trim($line[2]) : '';
        $phone = isset($line[3]) ? trim($line[3]) : '';
        $gender = isset($line[4]) ? strtolower(trim($line[4])) : null;
        $dob = isset($line[5]) ? trim($line[5]) : null;
        $address = isset($line[6]) ? trim($line[6]) : '';
        $guardianName = isset($line[7]) ? trim($line[7]) : '';
        $guardianPhone = isset($line[8]) ? trim($line[8]) : '';
        
        // Validate required fields
        $missing = [];
        if (empty($name)) $missing[] = 'Name';
        if (empty($email)) $missing[] = 'Email';
        if (empty($matric)) $missing[] = 'Matric No';
        
        if (!empty($missing)) {
            $errors[] = "Row $row: Missing required field(s): " . implode(', ', $missing);
            continue;
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Row $row ($name): Invalid email address '$email'";
            continue;
        }
        
        // Validate gender if provided
        if ($gender && !in_array($gender, ['male', 'female', 'other'])) {
            $gender = null; // Just ignore invalid gender instead of failing
        }
        
        // Use form class_id or null
        $classId = $formClassId ?: null;
        
        try {
            $stmt = $db->prepare("INSERT INTO students (name, email, matric_no, class_id, phone, gender, date_of_birth, address, guardian_name, guardian_phone, password, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')");
            $stmt->execute([$name, strtolower($email), $matric, $classId, $phone, $gender, $dob ?: null, $address, $guardianName, $guardianPhone, hashPassword('password')]);
            $studentId = $db->lastInsertId();
            $imported++;
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                // Check which field is duplicate
                $existing = $db->prepare("SELECT email, matric_no FROM students WHERE email = ? OR matric_no = ?");
                $existing->execute([strtolower($email), $matric]);
                $dup = $existing->fetch();
                if ($dup && strtolower($dup['email']) === strtolower($email)) {
                    $errors[] = "Row $row ($name): Email '$email' already exists in the system";
                } else {
                    $errors[] = "Row $row ($name): Matric number '$matric' already exists in the system";
                }
            } else {
                $errors[] = "Row $row ($name): Database error - " . $e->getMessage();
            }
        }
    }
    fclose($handle);
    
    logActivity('admin', $_SESSION['user_id'], 'import_students', "Imported $imported students via CSV");
    
    if ($imported === 0 && !empty($errors)) {
        jsonResponse(['success' => false, 'message' => 'No students were imported. Please check the errors below.', 'imported' => 0, 'errors' => $errors]);
    }
    
    $msg = "$imported student(s) imported successfully.";
    if (!empty($errors)) $msg .= ' ' . count($errors) . ' row(s) had errors: ' . $errors[0] . (count($errors) > 1 ? ' (and ' . (count($errors) - 1) . ' more)' : '');
    
    jsonResponse(['success' => $imported > 0, 'message' => $msg, 'imported' => $imported, 'errors' => $errors]);
}

function restrictStudent($id) {
    $db = getDB();
    $data = getPostData();
    $type = $data['restriction_type'] ?? '';
    $reason = sanitize($data['reason'] ?? '');
    
    if (!$type || !$reason) jsonResponse(['success' => false, 'message' => 'Restriction type and reason are required.'], 400);
    
    $validTypes = ['login', 'results', 'exams', 'login,results', 'login,exams', 'results,exams', 'login,results,exams'];
    if (!in_array($type, $validTypes)) jsonResponse(['success' => false, 'message' => 'Invalid restriction type.'], 400);
    
    $db->prepare("UPDATE students SET status = 'restricted', restriction_type = ?, restriction_reason = ?, restricted_at = NOW(), restricted_by = ? WHERE id = ?")->execute([$type, $reason, $_SESSION['user_id'], $id]);
    logActivity('admin', $_SESSION['user_id'], 'restrict_student', "Restricted student #$id: $type - $reason");
    jsonResponse(['success' => true, 'message' => 'Student has been restricted.']);
}

function unrestrrictStudent($id) {
    $db = getDB();
    $db->prepare("UPDATE students SET status = 'active', restriction_type = NULL, restriction_reason = NULL, restricted_at = NULL, restricted_by = NULL WHERE id = ?")->execute([$id]);
    logActivity('admin', $_SESSION['user_id'], 'unrestrict_student', "Unrestricted student #$id");
    jsonResponse(['success' => true, 'message' => 'Student restriction has been removed.']);
}
