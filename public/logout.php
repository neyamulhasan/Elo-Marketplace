<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';

destroyUserSession();
setFlashMessage('success', 'You have been logged out successfully.');
header('Location: ' . BASE_URL . '/public/');
exit();
?>
