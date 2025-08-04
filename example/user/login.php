<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

use AlperRagib\Ticimax\Ticimax;


$ticimax = new Ticimax(TICIMAX_MAIN_DOMAIN, TICIMAX_API_KEY);
$userService = $ticimax->userService();

echo "=== User Login ===\n";
$loginResult = $userService->login('john.doe@example.com', 'password123');

if ($loginResult->isSuccess()) {
    print($loginResult->message);
    print_r($loginResult->data);
}
