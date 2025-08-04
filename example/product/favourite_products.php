<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

use AlperRagib\Ticimax\Ticimax;


$ticimax = new Ticimax(TICIMAX_MAIN_DOMAIN, TICIMAX_API_KEY);
$favoriteProductService = $ticimax->favouriteProductService();


echo "=== Add Favourite Product Example ===\n";

$userId = 1050;
$productCardId = 6;
$quantity = 1.0;

$responseAddFavouriteProduct = $favoriteProductService->addFavouriteProduct($userId, $productCardId, $quantity);
print($responseAddFavouriteProduct->getMessage());


echo "=== Remove Favourite Product Example ===\n";

$userId = 1050;
$favouriteProductId = 72;

$responseRemoveFavouriteProduct = $favoriteProductService->removeFavouriteProduct($userId, $favouriteProductId);
print($responseRemoveFavouriteProduct->getMessage());


echo "\n=== List Favorite Products ===\n";

$parameters = [
    'BaslangicTarihi'       => null,
    'BitisTarihi'           => null,
    'KayitSayisi'           => 20,
    'SayfaNo'               => 1,
    'UyeID'                 => 1050,
];

$response = $favoriteProductService->getFavouriteProducts($parameters);

if ($response->isSuccess()) {
    foreach ($response->data as $product) {
        echo (
            ($product->UrunAdi ?? '[No UrunAdi]') .
            ' (UrunKartiID: ' . ($product->UrunKartiID ?? '[No UrunKartiID]') .
            ', FavoriUrunID: ' . ($product->FavoriUrunID ?? '[No FavoriUrunID]') .
            ', UrunFiyati: ' . ($product->UrunFiyati ?? '[No UrunFiyati]') .
            ")\n"
        );
    }
}
