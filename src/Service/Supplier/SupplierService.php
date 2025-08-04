<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Service\Supplier;

use AlperRagib\Ticimax\Model\BaseModel;
use AlperRagib\Ticimax\Model\ApiResponse;
use AlperRagib\Ticimax\TicimaxRequest;
use SoapFault;

/**
 * Class SupplierService
 * Handles supplier-related API operations.
 */
class SupplierService
{
    private TicimaxRequest $request;
    private string $apiUrl = "/Servis/UrunServis.svc?singleWsdl";

    public function __construct(TicimaxRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Fetch suppliers from the API.
     * @param int $supplierId (optional) Specific supplier ID, defaults to 0 for all suppliers
     * @return ApiResponse
     */
    public function getSuppliers(int $supplierId = 0): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        $suppliers = [];
        try {
            $params = [
                'UyeKodu' => $this->request->key,
                'tedarikciID' => $supplierId,
            ];

            $response = $client->__soapCall("SelectTedarikci", [
                'parameters' => $params
            ]);

            $supplierArr = $response->SelectTedarikciResult->Tedarikci ?? [];

            if (is_object($supplierArr)) {
                $supplierArr = [$supplierArr];
            }

            foreach ($supplierArr as $sup) {
                $suppliers[] = new BaseModel($sup);
            }
            
            return ApiResponse::success(
                $suppliers,
                'Suppliers retrieved successfully.'
            );
        } catch (SoapFault $e) {
            return ApiResponse::error('Error retrieving suppliers: ' . $e->getMessage());
        }
    }
}
