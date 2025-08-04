<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

use AlperRagib\Ticimax\Ticimax;


$ticimax = new Ticimax(TICIMAX_MAIN_DOMAIN, TICIMAX_API_KEY);
$locationService = $ticimax->locationService();

echo "\n--- List Countries ---\n";

$countries = $locationService->getCountries(1);

if ($countries->isSuccess()) {
    foreach ($countries->data as $country) {
        echo (
            ($country->Tanim ?? '[No Tanim]') .
            ' (ID: ' . ($country->ID ?? '[No ID]') .
            ")\n"
        );
    }
}

echo "\n--- List Cities ---\n";

$cities = $locationService->getCities(null, 1);
if ($cities->isSuccess()) {
    foreach ($cities->data as $city) {
        echo (
            ($city->Tanim ?? '[No Tanim]') .
            ' (ID: ' . ($city->ID ?? '[No ID]') .
            ")\n"
        );
    }
}

echo "\n--- List Districts ---\n";

// Get districts for a specific city (e.g., city ID 1)
$districts = $locationService->getDistricts(null, 34);
if ($districts->isSuccess()) {
    foreach ($districts->data as $district) {
        echo (
            ($district->Tanim ?? '[No Tanim]') .
            ' (ID: ' . ($district->ID ?? '[No ID]') .
            ")\n"
        );
    }
}
