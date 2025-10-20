<?php
/**
 * Validation Helper Functions
 * Server-side validation for forms
 */

/**
 * Validate email address
 * @param string $email
 * @return bool
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password strength
 * @param string $password
 * @return bool
 */
function validatePassword($password) {
    return strlen($password) >= PASSWORD_MIN_LENGTH;
}

/**
 * Validate phone number (Bangladesh format)
 * @param string $phone
 * @return bool
 */
function validatePhone($phone) {
    // Bangladesh phone: 11 digits starting with 01
    return preg_match('/^01[0-9]{9}$/', $phone);
}

/**
 * Sanitize string input
 * @param string $input
 * @return string
 */
function sanitizeString($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate required field
 * @param string $value
 * @return bool
 */
function validateRequired($value) {
    return !empty(trim($value));
}

/**
 * Validate number
 * @param mixed $value
 * @return bool
 */
function validateNumber($value) {
    return is_numeric($value) && $value >= 0;
}

/**
 * Validate price
 * @param mixed $price
 * @return bool
 */
function validatePrice($price) {
    return is_numeric($price) && $price > 0;
}

/**
 * Validate image upload
 * @param array $file $_FILES array element
 * @return array ['valid' => bool, 'error' => string]
 */
function validateImage($file) {
    $result = ['valid' => false, 'error' => ''];
    
    // Check if file was uploaded
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        $result['error'] = 'No file uploaded';
        return $result;
    }
    
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        $result['error'] = 'File size exceeds maximum allowed (5MB)';
        return $result;
    }
    
    // Check file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime, ALLOWED_IMAGE_TYPES)) {
        $result['error'] = 'Invalid file type. Only JPEG, PNG, and GIF allowed';
        return $result;
    }
    
    $result['valid'] = true;
    return $result;
}

/**
 * Generate random string
 * @param int $length
 * @return string
 */
function generateRandomString($length = 10) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Validate date format
 * @param string $date
 * @param string $format
 * @return bool
 */
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * Clean filename for upload
 * @param string $filename
 * @return string
 */
function cleanFilename($filename) {
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
    return time() . '_' . $filename;
}

/**
 * Escape output for HTML
 * @param string $string
 * @return string
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Format price for display
 * @param float $price
 * @return string
 */
function formatPrice($price) {
    return '৳' . number_format($price, 2);
}

/**
 * Format date for display
 * @param string $date
 * @return string
 */
function formatDate($date) {
    return date('d M, Y', strtotime($date));
}
?>
