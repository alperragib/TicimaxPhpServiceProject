<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Service\Menu;

use AlperRagib\Ticimax\Model\BaseModel;
use AlperRagib\Ticimax\Model\ApiResponse;
use AlperRagib\Ticimax\TicimaxRequest;
use SoapFault;

/**
 * Class MenuService
 * Handles menu-related API operations.
 */
class MenuService
{
    private TicimaxRequest $request;
    private string $apiUrl = "/Servis/CustomServis.svc?singleWsdl";

    public function __construct(TicimaxRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Fetch menus from the API.
     * @return ApiResponse
     */
    public function getMenus(array $parameters = []): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        try {
            $defaultParameters = [
                'Aktif'       => null,
                'MenuID'      => null,
                'Dil'         => null,
            ];

            $parameters = array_merge($defaultParameters, $parameters);
            $response = $client->__soapCall("GetMenu", [
                [
                    'UyeKodu' => $this->request->key,
                    'request' => (object)$parameters,
                ]
            ]);

            $menuArr = $response->GetMenuResult->Menuler->WebMenu ?? [];
            $menus = [];

            if (is_object($menuArr)) {
                $menuArr = [$menuArr];
            }

            foreach ($menuArr as $menu) {
                $menus[] = new BaseModel($menu);
            }

            return ApiResponse::success(
                $menus,
                'Menus retrieved successfully.'
            );
        } catch (SoapFault $e) {
            return ApiResponse::error('Error retrieving menus: ' . $e->getMessage());
        }
    }
}
