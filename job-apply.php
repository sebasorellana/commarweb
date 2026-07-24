<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/site.php';
require_once __DIR__ . '/includes/jobs.php';
require_once __DIR__ . '/includes/media.php';
require_once __DIR__ . '/includes/integrations.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit;
}

if (!commar_verify_csrf()) {
    header('Location: ' . commar_url('trabaja-con-nosotros.php?status=error'));
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

if (
    !$job
    || $fullName === ''
    || strlen($fullName) > 160
    || !filter_var($email, FILTER_VALIDATE_EMAIL)
    || strlen($email) > 255
    || strlen($phone) > 80
    || strlen($message) > 5000
    || !$file
    || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK
) {
    header('Location: ' . commar_url('trabaja-con-nosotros.php?status=error'));
    exit;
}

if (!commar_recaptcha_verify('job_apply')) {
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

$mimeDetector = new finfo(FILEINFO_MIME_TYPE);
$detectedMime = $mimeDetector->file((string) $file['tmp_name']);
$allowedMimes = [
    'pdf' => ['application/pdf'],
    'doc' => ['application/msword', 'application/x-ole-storage', 'application/CDFV2', 'application/vnd.ms-office'],
    'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
];
if (!is_string($detectedMime) || !in_array($detectedMime, $allowedMimes[$extension], true)) {
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

try {
    commar_media_register($relativePath, 'document', 0, 0, $originalName);

    $statement = commar_db()->prepare(
        'INSERT INTO commar_job_applications
         (job_id, full_name, email, phone, message, cv_path, cv_original_name, ip_address, user_agent, submitted_at)
         VALUES
         (:job_id, :full_name, :email, :phone, :message, :cv_path, :cv_original_name, :ip_address, :user_agent, :submitted_at)'
    );
    $statement->execute([
        'job_id' => $jobId,
        'full_name' => $fullName,
        'email' => strtolower($email),
        'phone' => $phone,
        'message' => $message,
        'cv_path' => $relativePath,
        'cv_original_name' => substr($originalName, 0, 255),
        'ip_address' => substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45),
        'user_agent' => substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
        'submitted_at' => date('Y-m-d H:i:s'),
    ]);
} catch (Throwable $exception) {
    if (is_file($targetPath)) {
        unlink($targetPath);
    }
    error_log('No se pudo guardar una postulación: ' . $exception->getMessage());
    header('Location: ' . commar_url('trabaja-con-nosotros.php?status=error'));
    exit;
}

header('Location: ' . commar_url('trabaja-con-nosotros.php?status=ok'));
exit;
