<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

use AlperRagib\Ticimax\Ticimax;


$ticimax = new Ticimax(TICIMAX_MAIN_DOMAIN, TICIMAX_API_KEY);
$supplierService = $ticimax->supplierService();

echo "\n--- List Suppliers ---\n";
$response = $supplierService->getSuppliers();
if ($response->isSuccess()) {
    foreach ($response->data as $supplier) {
        echo (
            ($supplier->Tanim ?? '[No Tanim]') .
            ' (ID: ' . ($supplier->ID ?? '[No ID]') .
            ")\n"
        );
    }
}
