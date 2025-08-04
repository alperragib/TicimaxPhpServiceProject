<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Service\Category;

use AlperRagib\Ticimax\Model\BaseModel;
use AlperRagib\Ticimax\Model\ApiResponse;
use AlperRagib\Ticimax\TicimaxRequest;
use SoapFault;

/**
 * Class CategoryService
 * Handles category-related API operations.
 */
class CategoryService
{
    private TicimaxRequest $request;
    private string $apiUrl = "/Servis/UrunServis.svc?singleWsdl";

    public function __construct(TicimaxRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Fetch categories from the API.
     * @param int $categoryId (optional) Specific category ID, defaults to 0 for all categories
     * @param int|null $parentId (optional) Parent category ID for filtering
     * @param string|null $language (optional) Language code for filtering
     * @return ApiResponse
     */
    public function getCategories(int $categoryId = 0, ?int $parentId = null, ?string $language = null): ApiResponse
    {

        $client = $this->request->soap_client($this->apiUrl);
        $categories = [];
        try {
            $params = [
                'UyeKodu'    => $this->request->key,
                'kategoriID' => $categoryId,
                'parentID' => $parentId,
                'dil' => $language,
            ];

            $response = $client->__soapCall("SelectKategori", [
                'parameters' => $params
            ]);

            $catArr = $response->SelectKategoriResult->Kategori ?? [];

            if (is_object($catArr)) {
                $catArr = [$catArr];
            }

            foreach ($catArr as $cat) {
                $categories[] = new BaseModel($cat);
            }

            return ApiResponse::success(
                $categories,
                'Categories retrieved successfully.'
            );
        } catch (SoapFault $e) {
            return ApiResponse::error('Error retrieving categories: ' . $e->getMessage());
        }
    }
}
