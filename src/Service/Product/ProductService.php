<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Service\Product;

use AlperRagib\Ticimax\Model\BaseModel;
use AlperRagib\Ticimax\Model\ApiResponse;
use AlperRagib\Ticimax\TicimaxRequest;
use SoapFault;

/**
 * Class ProductService
 * Handles product-related API operations.
 */
class ProductService
{
    private TicimaxRequest $request;
    private string $apiUrl = "/Servis/UrunServis.svc?singleWsdl";

    public function __construct(TicimaxRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Fetch products from the API.
     * @param array $filters Product filters
     * @param array $pagination Pagination settings
     * @return ApiResponse
     */
    public function getProducts(array $filters = [], array $pagination = []): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        try {
            $products = [];

            $defaultFilters = [
                'Aktif'                    => -1,
                'Firsat'                   => -1,
                'Indirimli'                => -1,
                'Vitrin'                   => -1,
                'KategoriID'               => 0,
                'MarkaID'                  => 0,
                'UrunKartiID'              => 0,
                'ToplamStokAdediBas'       => null,
                'ToplamStokAdediSon'       => null,
                'TedarikciID'              => 0,
                'Dil'                      => null,
            ];

            $defaultPagination = [
                'BaslangicIndex' => 0,
                'KayitSayisi' => 20,
                'SiralamaDegeri' => 'ID',
                'SiralamaYonu' => 'DESC'
            ];

            $urunFiltre = array_merge($defaultFilters, $filters);
            $urunSayfalama = array_merge($defaultPagination, $pagination);

            $response = $client->__soapCall("SelectUrun", [
                [
                    'UyeKodu' => $this->request->key,
                    'f' => (object)$urunFiltre,
                    's' => (object)$urunSayfalama
                ]
            ]);

            $urunler = $response->SelectUrunResult->UrunKarti ?? [];
            if (is_object($urunler)) {
                $urunler = [$urunler];
            }

            foreach ($urunler as $urun) {
                $products[] = new BaseModel($urun);
            }

            return ApiResponse::success(
                $products,
                'Products retrieved successfully.'
            );
        } catch (SoapFault $e) {
            return ApiResponse::error('Error retrieving products: ' . $e->getMessage());
        }
    }

    /**
     * Get product variations
     * @param array $filters Variation filters
     * @param array $pagination Pagination settings
     * @return ApiResponse
     */
    public function getProductVariations(array $filters = [], array $pagination = []): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        $variations = [];
        try {
            $defaultFilters = [
                'Aktif' => -1,
                'UrunKartiID' => 0,
                'VaryasyonID' => 0,
            ];

            $defaultPagination = [
                'BaslangicIndex' => 0,
                'KayitSayisi' => 20,
                'SiralamaDegeri' => 'ID',
                'SiralamaYonu' => 'DESC'
            ];

            $varyasyonFiltre = array_merge($defaultFilters, $filters);
            $varyasyonSayfalama = array_merge($defaultPagination, $pagination);

            $response = $client->__soapCall("SelectVaryasyon", [
                [
                    'UyeKodu' => $this->request->key,
                    'f' => (object)$varyasyonFiltre,
                    's' => (object)$varyasyonSayfalama
                ]
            ]);

            $varyasyonlar = $response->SelectVaryasyonResult->Varyasyon ?? [];
            if (is_object($varyasyonlar)) {
                $varyasyonlar = [$varyasyonlar];
            }

            foreach ($varyasyonlar as $variation) {
                // Convert each variation to BaseModel
                $variations[] = new BaseModel($variation);
            }

            return ApiResponse::success($variations, 'Product variations retrieved successfully.');
        } catch (SoapFault $e) {
            return ApiResponse::error('Error retrieving product variations: ' . $e->getMessage());
        }
    }

    /**
     * Get product payment options
     * @param int $varyasyonId Product variation ID
     * @return ApiResponse
     */
    public function getProductPaymentOptions(int $varyasyonId): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        try {
            $params = [
                'UyeKodu' => $this->request->key,
                'varyasyonId' => $varyasyonId,
            ];

            $response = $client->__soapCall("SelectUrunOdemeSecenek", [
                'parameters' => $params
            ]);

            $options = $response->SelectUrunOdemeSecenekResult->UrunOdemeSecenek ?? [];

            if (is_object($options)) {
                $options = [$options];
            }

            foreach ($options as $option) {
                // Convert each variation to BaseModel
                $options[] = new BaseModel($option);
            }

            return ApiResponse::success(
                $options,
                'Product payment options retrieved successfully.'
            );
        } catch (SoapFault $e) {
            return ApiResponse::error('Error retrieving product payment options: ' . $e->getMessage());
        }
    }
}
