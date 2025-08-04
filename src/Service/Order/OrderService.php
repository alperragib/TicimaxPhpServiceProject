<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Service\Order;

use AlperRagib\Ticimax\Model\BaseModel;
use AlperRagib\Ticimax\Model\ApiResponse;
use AlperRagib\Ticimax\TicimaxRequest;
use SoapFault;

/**
 * Class OrderService
 * Handles order-related API operations.
 */
class OrderService
{
    private TicimaxRequest $request;
    private string $apiUrl = "/Servis/SiparisServis.svc?singleWsdl";

    public function __construct(TicimaxRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Fetch orders from the API.
     * @param array $filters Order filters
     * @param array $pagination Pagination settings
     * @return ApiResponse
     */
    public function getOrders(array $filters = [], array $pagination = []): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        $orders = [];
        try {
            $defaultFilters = [
                'SiparisID' => 0,
                'UyeID' => 0,
                'SiparisTarihiBas' => null,
                'SiparisTarihiSon' => null,
            ];

            $defaultPagination = [
                'BaslangicIndex' => 0,
                'KayitSayisi' => 20,
                'SiralamaDegeri' => 'ID',
                'SiralamaYonu' => 'DESC'
            ];

            $siparisFiltre = array_merge($defaultFilters, $filters);
            $siparisSayfalama = array_merge($defaultPagination, $pagination);

            $response = $client->__soapCall("SelectSiparis", [
                [
                    'UyeKodu' => $this->request->key,
                    'f' => (object)$siparisFiltre,
                    's' => (object)$siparisSayfalama
                ]
            ]);

            $siparisler = $response->SelectSiparisResult->WebSiparis ?? [];
            if (is_object($siparisler)) {
                $siparisler = [$siparisler];
            }

            foreach ($siparisler as $order) {
                $orders[] = new BaseModel($order);
            }

            return ApiResponse::success(
                $orders,
                'Orders retrieved successfully.'
            );
        } catch (SoapFault $e) {
            return ApiResponse::error('Error retrieving orders: ' . $e->getMessage());
        }
    }

    /**
     * Create a new order.
     * @param array $order
     * @return ApiResponse
     */
    public function createOrder(array $order): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        try {
            $params = [
                'UyeKodu' => $this->request->key,
                'siparis' => (object)$order,
            ];

            $response = $client->__soapCall("SaveSiparis", [
                'parameters' => $params
            ]);

            $result = $response->SaveSiparisResult ?? [];

            if ((isset($result->IsError) && $result->IsError) || !isset($result->SiparisDetayi)) {
                $message = !empty($result->ErrorMessage)
                    ? trim($result->ErrorMessage, '. ') . '.'
                    : 'Error creating order.';

                return ApiResponse::error($message);
            }

            return ApiResponse::success(
                new BaseModel($result->SiparisDetayi),
                'Order created successfully.'
            );
        } catch (SoapFault $e) {
            return ApiResponse::error('Error creating order: ' . $e->getMessage());
        }
    }
}
