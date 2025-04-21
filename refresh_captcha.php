<?php
session_start();

// Generate new CAPTCHA
$captcha_num = rand(1000, 9999);
$_SESSION['captcha'] = $captcha_num;

// Return the new CAPTCHA as JSON
header('Content-Type: application/json');
echo json_encode(['captcha' => $captcha_num]);
?> 