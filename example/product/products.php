<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

use AlperRagib\Ticimax\Ticimax;


$ticimax = new Ticimax(TICIMAX_MAIN_DOMAIN, TICIMAX_API_KEY);
$productService = $ticimax->productService();


echo "\n--- List Products ---\n";

$filters = [
    'Aktif'                    => 1,    // -1: no filter, 0: false, 1: true
    'Firsat'                   => -1,   // -1: no filter, 0: false, 1: true
    'Indirimli'                => -1,   // -1: no filter, 0: false, 1: true
    'Vitrin'                   => -1,   // -1: no filter, 0: false, 1: true
    'KategoriID'               => 0,    // 0: no filter
    'MarkaID'                  => 0,    // 0: no filter
    'UrunKartiID'              => 0,    // 0: no filter
    'ToplamStokAdediBas'       => null, // Starting stock amount (double)
    'ToplamStokAdediSon'       => null, // Ending stock amount (double)
    'TedarikciID'              => 0,    // 0: no filter
    'Dil'                      => 'tr',
];

$pagination = [
    'BaslangicIndex'            => 0,
    'KayitSayisi'               => 10,
    'KayitSayisinaGoreGetir'    => true,
    'SiralamaDegeri'            => 'Sira',
    'SiralamaYonu'              => 'ASC',
];

$response = $productService->getProducts($filters, $pagination);

if ($response->isSuccess()) {
    foreach ($response->data as $product) {
        echo (
            ($product->UrunAdi ?? '[No UrunAdi]') .
            ' (ID: ' . ($product->ID ?? '[No ID]') .
            ', ToplamStokAdedi: ' . ($product->ToplamStokAdedi ?? '[No ToplamStokAdedi]') .
            ")\n"
        );
    }
}



echo "\n--- List Product Variations ---\n";

$filtersVariations = [
    'Aktif' => 1,
    'UrunKartiID' => 0,
    'VaryasyonID' => 0,
];

$paginationVariations = [
    'BaslangicIndex' => 0,
    'KayitSayisi' => 10,
    'SiralamaDegeri' => 'ID',
    'SiralamaYonu' => 'DESC'
];


$responseVariations = $productService->getProductVariations($filtersVariations, $paginationVariations);

if ($responseVariations->isSuccess()) {
    foreach ($responseVariations->data as $productVariation) {
        echo (
            ($productVariation->UrunKartiID ?? '[No UrunKartiID]') .
            ' (ID: ' . ($productVariation->ID ?? '[No ID]') .
            ' (IndirimliFiyati: ' . ($productVariation->IndirimliFiyati ?? '[No IndirimliFiyati]') .
            ' (SatisFiyati: ' . ($productVariation->SatisFiyati ?? '[No SatisFiyati]') .
            ' (KargoUcreti: ' . ($productVariation->KargoUcreti ?? '[No KargoUcreti]') .
            ' (ParaBirimi: ' . ($productVariation->ParaBirimi ?? '[No ParaBirimi]') .
            ', StokAdedi: ' . ($productVariation->StokAdedi ?? '[No StokAdedi]') .
            ")\n"
        );
    }
}


echo "\n--- List Product Payment Options ---\n";

$responsePaymentOptions = $productService->getProductPaymentOptions(0);

if ($responsePaymentOptions->isSuccess()) {

    foreach ($responsePaymentOptions->data as $paymentOption) {

        echo (
            ($paymentOption->BankaAdi ?? '[No BankaAdi]') .
            ' (BankaID: ' . ($paymentOption->BankaID ?? '[No BankaID]') .
            ")\n"
        );
    }
}
