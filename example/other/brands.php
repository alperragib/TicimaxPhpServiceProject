<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

use AlperRagib\Ticimax\Ticimax;


$ticimax = new Ticimax(TICIMAX_MAIN_DOMAIN, TICIMAX_API_KEY);
$brandService = $ticimax->brandService();

echo "\n--- List Brands ---\n";
$response = $brandService->getBrands();
if ($response->isSuccess()) {
    foreach ($response->data as $brand) {
        echo (
            ($brand->Tanim ?? '[No Tanim]') .
            ' (ID: ' . ($brand->ID ?? '[No ID]') .
            ")\n"
        );
    }
}
