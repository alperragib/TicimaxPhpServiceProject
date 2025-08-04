<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

use AlperRagib\Ticimax\Ticimax;


$ticimax = new Ticimax(TICIMAX_MAIN_DOMAIN, TICIMAX_API_KEY);
$menuService = $ticimax->menuService();

echo "\n--- List Brands ---\n";

$parameters = [
    'Aktif'       => null,
    'MenuID'      => null,
    'Dil'         => null,
];

$response = $menuService->getMenus($parameters);

if ($response->isSuccess()) {
    foreach ($response->data as $menu) {
        echo (
            ($menu->Baslik ?? '[No Baslik]') .
            ' (ID: ' . ($menu->ID ?? '[No ID]') .
            ")\n"
        );
    }
}
