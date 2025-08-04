<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Service\Product;

use AlperRagib\Ticimax\Model\BaseModel;
use AlperRagib\Ticimax\Model\ApiResponse;
use AlperRagib\Ticimax\TicimaxRequest;
use SoapFault;

/**
 * Class FavouriteProductService
 * Handles FavouriteProduct-related API operations.
 */
class FavouriteProductService
{
    private TicimaxRequest $request;
    private string $apiUrl = "/Servis/CustomServis.svc?singleWsdl";

    public function __construct(TicimaxRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Fetch FavouriteProducts from the API.
     * @param array $parameters
     * @return ApiResponse
     */
    public function getFavouriteProducts(array $parameters = []): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        $favouriteProducts = [];
        try {
            $defaultParameters = [
                'BaslangicTarihi'       => null,
                'BitisTarihi'           => null,
                'KayitSayisi'           => 20,
                'SayfaNo'               => 1,
                'UyeID'                 => 0,
            ];

            $parameters = array_merge($defaultParameters, $parameters);

            $response = $client->__soapCall("GetFavoriUrunler", [
                [
                    'UyeKodu' => $this->request->key,
                    'request' => (object)$parameters,
                ]
            ]);

            // Check for API error first
            if (isset($response->GetFavoriUrunlerResult->IsError) && $response->GetFavoriUrunlerResult->IsError) {
                return ApiResponse::error($response->GetFavoriUrunlerResult->ErrorMessage ?? 'Unknown error occurred');
            }

            $result = $response->GetFavoriUrunlerResult->Urunler ?? null;

            // Handle the Urunler structure
            if (is_object($result) && isset($result->WebFavoriUrunler)) {
                $webFavoriUrunler = $result->WebFavoriUrunler;

                // If single product, convert to array
                if (!is_array($webFavoriUrunler)) {
                    $webFavoriUrunler = [$webFavoriUrunler];
                }

                // Process each product
                foreach ($webFavoriUrunler as $favoriUrun) {
                    if (is_object($favoriUrun)) {
                        $favouriteProducts[] = new BaseModel((array)$favoriUrun);
                    }
                }
            }

            return ApiResponse::success($favouriteProducts, 'Favourite products retrieved successfully.');
        } catch (SoapFault $e) {
            return ApiResponse::error('Error retrieving favourite products: ' . $e->getMessage());
        }
    }

    /**
     * Add favourite product.
     * @param int $userId User ID
     * @param int $productCardId Product card ID
     * @param float $quantity Quantity (default: 1.0)
     * @return ApiResponse
     */
    public function addFavouriteProduct(int $userId, int $productCardId, float $quantity = 1.0): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);

        try {
            $requestData = [
                'Adet' => $quantity,
                'UrunKartiID' => $productCardId,
                'UyeID' => $userId,
            ];

            $response = $client->__soapCall("AddFavoriUrun", [
                [
                    'UyeKodu' => $this->request->key,
                    'request' => (object)$requestData,
                ]
            ]);

            if (isset($response->AddFavoriUrunResult)) {
                $result = $response->AddFavoriUrunResult;

                // IsError check - boolean field according to WSDL
                $isError = $result->IsError ?? false; // Default false (consider as successful)

                if ($isError === true) {
                    return ApiResponse::error($result->ErrorMessage ?? 'Unknown error occurred');
                }

                return ApiResponse::success($result, 'Favourite product added successfully.');
            }

            return ApiResponse::error('AddFavoriUrunResult not found - API response structure differs from expected.');
        } catch (SoapFault $e) {
            error_log("AddFavoriUrun SoapFault: " . $e->getMessage());
            return ApiResponse::error('Error adding favourite product: ' . $e->getMessage());
        }
    }

    /**
     * Remove favourite product.
     * @param int $userId User ID
     * @param int $favouriteProductId Favourite product ID
     * @return ApiResponse
     */
    public function removeFavouriteProduct(int $userId, int $favouriteProductId): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);

        try {
            $requestData = [
                'UyeID' => $userId,
                'FavoriUrunID' => $favouriteProductId,
            ];

            $response = $client->__soapCall("RemoveFavoriUrun", [
                [
                    'UyeKodu' => $this->request->key,
                    'request' => (object)$requestData,
                ]
            ]);

            if (isset($response->RemoveFavoriUrunResult)) {
                $result = $response->RemoveFavoriUrunResult;
                if ($result->IsError ?? true) {
                    return ApiResponse::error($result->ErrorMessage ?? 'Unknown error occurred');
                }
                return ApiResponse::success($result, 'Favourite product removed successfully.');
            }

            return ApiResponse::error('Failed to remove favourite product.');
        } catch (SoapFault $e) {
            return ApiResponse::error('Error removing favourite product: ' . $e->getMessage());
        }
    }
}
