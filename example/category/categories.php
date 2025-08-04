<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

use AlperRagib\Ticimax\Ticimax;


$ticimax = new Ticimax(TICIMAX_MAIN_DOMAIN, TICIMAX_API_KEY);
$categoryService = $ticimax->categoryService();

echo "\n--- List Categories ---\n";
$response = $categoryService->getCategories(0, null, 'tr');


if ($response->isSuccess()) {
    foreach ($response->data as $category) {
        echo (
            ($category->Tanim ?? '[No Tanim]') .
            ' (ID: ' . ($category->ID ?? '[No ID]') .
            ', PID: ' . ($category->PID ?? '[No PID]') .
            ")\n"
        );
    }
}
