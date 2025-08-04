<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Service\Brand;

use AlperRagib\Ticimax\Model\BaseModel;
use AlperRagib\Ticimax\Model\ApiResponse;
use AlperRagib\Ticimax\TicimaxRequest;
use SoapFault;

/**
 * Class BrandService
 * Handles brand-related API operations.
 */
class BrandService
{
    private TicimaxRequest $request;
    private string $apiUrl = "/Servis/UrunServis.svc?singleWsdl";

    public function __construct(TicimaxRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Fetch brands from the API.
     * @param int $brandId (optional) Specific brand ID, defaults to 0 for all brands
     * @return ApiResponse
     */
    public function getBrands(int $brandId = 0,): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        $brands = [];
        try {
            $params = [
                'UyeKodu'    => $this->request->key,
                'markaID'    => $brandId,
            ];

            $response = $client->__soapCall("SelectMarka", [
                'parameters' => $params
            ]);

            $brandArr = $response->SelectMarkaResult->Marka ?? [];

            if (is_object($brandArr)) {
                $brandArr = [$brandArr];
            }

            foreach ($brandArr as $brand) {
                $brands[] = new BaseModel($brand);
            }
            
            return ApiResponse::success(
                $brands,
                'Brands retrieved successfully.'
            );
        } catch (SoapFault $e) {
            return ApiResponse::error('Error retrieving brands: ' . $e->getMessage());
        }
    }
}
