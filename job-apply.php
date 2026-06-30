<?php
require_once __DIR__ . '/includes/site.php';
require_once __DIR__ . '/includes/jobs.php';
require_once __DIR__ . '/includes/media.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . commar_url('trabaja-con-nosotros.php'));
    exit;
}

if (trim((string) ($_POST['company_name'] ?? '')) !== '') {
    header('Location: ' . commar_url('trabaja-con-nosotros.php?status=ok'));
    exit;
}

$jobId = (int) ($_POST['job_id'] ?? 0);
$job = commar_job_by_id($jobId, true);
$fullName = trim((string) ($_POST['full_name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));
$file = $_FILES['cv'] ?? null;

if (!$job || $fullName === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    header('Location: ' . commar_url('trabaja-con-nosotros.php?status=error'));
    exit;
}

$maxSize = 5 * 1024 * 1024;
if ((int) ($file['size'] ?? 0) <= 0 || (int) ($file['size'] ?? 0) > $maxSize) {
    header('Location: ' . commar_url('trabaja-con-nosotros.php?status=error'));
    exit;
}

$originalName = basename((string) ($file['name'] ?? 'cv'));
$extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
$allowedExtensions = ['pdf', 'doc', 'docx'];
if (!in_array($extension, $allowedExtensions, true)) {
    header('Location: ' . commar_url('trabaja-con-nosotros.php?status=error'));
    exit;
}

$uploadDir = __DIR__ . '/uploads/cv';
if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true)) {
    header('Location: ' . commar_url('trabaja-con-nosotros.php?status=error'));
    exit;
}

$fileName = 'cv-' . $jobId . '-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $extension;
$relativePath = 'uploads/cv/' . $fileName;
$targetPath = __DIR__ . '/' . $relativePath;

if (!move_uploaded_file((string) $file['tmp_name'], $targetPath)) {
    header('Location: ' . commar_url('trabaja-con-nosotros.php?status=error'));
    exit;
}

commar_media_register($relativePath, 'document', 0, 0, (string) ($file['name'] ?? 'CV'));

$statement = commar_db()->prepare(
    'INSERT INTO commar_job_applications
     (job_id, full_name, email, phone, message, cv_path, cv_original_name, ip_address, user_agent, submitted_at)
     VALUES
     (:job_id, :full_name, :email, :phone, :message, :cv_path, :cv_original_name, :ip_address, :user_agent, :submitted_at)'
);
$statement->execute([
    'job_id' => $jobId,
    'full_name' => $fullName,
    'email' => $email,
    'phone' => $phone,
    'message' => $message,
    'cv_path' => $relativePath,
    'cv_original_name' => $originalName,
    'ip_address' => substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45),
    'user_agent' => substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
    'submitted_at' => date('Y-m-d H:i:s'),
]);

header('Location: ' . commar_url('trabaja-con-nosotros.php?status=ok'));
exit;
