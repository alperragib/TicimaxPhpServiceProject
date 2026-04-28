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

    /**
     * Update an order's status (SetSiparisDurum).
     *
     * Ticimax does not expose a generic "update order" method; the only
     * mutation supported on an existing order is its status. Pass an OrderStatus
     * integer constant (e.g. OrderStatus::ODEME_BEKLIYOR) — it is translated
     * to the PascalCase string the WSDL's WebSiparisDurumlari enum expects.
     * A PascalCase string (e.g. "Onaylandi") is also accepted and passed through.
     *
     * @param int          $orderId      Ticimax SiparisID.
     * @param int|string   $status       Order status (OrderStatus::* or PascalCase string).
     * @param string       $kargoTakipNo Optional cargo tracking number.
     * @param bool         $notifyByMail Whether Ticimax should email the customer.
     * @return ApiResponse
     */
    public function setOrderStatus(
        int $orderId,
        $status,
        string $kargoTakipNo = '',
        bool $notifyByMail = false
    ): ApiResponse {
        $client = $this->request->soap_client($this->apiUrl);
        try {
            $response = $client->__soapCall('SetSiparisDurum', [[
                'UyeKodu' => $this->request->key,
                'request' => (object)[
                    'Durum'           => OrderStatus::nameFor($status),
                    'KargoTakipNo'    => $kargoTakipNo,
                    'MailBilgilendir' => $notifyByMail,
                    'SiparisID'       => $orderId,
                ],
            ]]);

            $result = $response->SetSiparisDurumResult ?? null;

            $isError = $result->IsErros ?? $result->IsError ?? false;
            if ($isError) {
                $message = !empty($result->ErrorMessage)
                    ? trim($result->ErrorMessage, '. ') . '.'
                    : 'Error updating order status.';
                return ApiResponse::error($message);
            }

            return ApiResponse::success(null, 'Order status updated successfully.');
        } catch (SoapFault $e) {
            return ApiResponse::error('Error updating order status: ' . $e->getMessage());
        }
    }

    /**
     * Mark an order as shipped (SetSiparisKargoyaVerildi).
     *
     * Shortcut for moving the order into the "Kargoya verildi" state. Equivalent
     * to setOrderStatus($orderId, OrderStatus::KARGOYA_VERILDI), but uses the
     * dedicated Ticimax method which also triggers shipping-side workflows.
     *
     * @param int $orderId Ticimax SiparisID.
     * @return ApiResponse
     */
    public function setOrderShipped(int $orderId): ApiResponse
    {
        return $this->callSimpleSetter('SetSiparisKargoyaVerildi', $orderId);
    }

    /**
     * Mark an order as delivered (SetSiparisTeslimEdildi).
     *
     * @param int $orderId Ticimax SiparisID.
     * @return ApiResponse
     */
    public function setOrderDelivered(int $orderId): ApiResponse
    {
        return $this->callSimpleSetter('SetSiparisTeslimEdildi', $orderId);
    }

    /**
     * Flag an order as transferred to an external system (SetSiparisAktarildi).
     *
     * @param int $orderId Ticimax SiparisID.
     * @return ApiResponse
     */
    public function setOrderTransferred(int $orderId): ApiResponse
    {
        return $this->callSimpleSetter('SetSiparisAktarildi', $orderId);
    }

    /**
     * Cancel the "transferred" flag on an order (SetSiparisAktarildiIptal).
     *
     * @param int $orderId Ticimax SiparisID.
     * @return ApiResponse
     */
    public function unsetOrderTransferred(int $orderId): ApiResponse
    {
        return $this->callSimpleSetter('SetSiparisAktarildiIptal', $orderId);
    }

    /**
     * Set the invoice number on an order (SetFaturaNo).
     *
     * @param int      $orderId      Ticimax SiparisID.
     * @param string   $invoiceNo    Invoice number to attach.
     * @param string   $invoiceDate  Optional invoice date as ISO 8601; empty leaves it unset.
     * @return ApiResponse
     */
    public function setInvoiceNumber(int $orderId, string $invoiceNo, string $invoiceDate = ''): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        try {
            $args = [
                'UyeKodu'   => $this->request->key,
                'SiparisID' => $orderId,
                'FaturaNo'  => $invoiceNo,
            ];
            if ($invoiceDate !== '') {
                $args['FaturaTarihi'] = $invoiceDate;
            }

            $response = $client->__soapCall('SetFaturaNo', [$args]);

            return ApiResponse::success($response, 'Invoice number set successfully.');
        } catch (SoapFault $e) {
            return ApiResponse::error('Error setting invoice number: ' . $e->getMessage());
        }
    }

    /**
     * Save / update a cargo tracking number for an order (SaveKargoTakipNo).
     *
     * @param int    $orderId             Ticimax SiparisID.
     * @param string $kargoTakipNo        Tracking number.
     * @param string $kargoKodu           Optional carrier code.
     * @param string $kargoTakipLink      Optional tracking URL.
     * @param string $barkodBilgisi       Optional barcode (auto-generated if empty).
     * @param bool   $kargoTakipLinkGoster Whether to expose the tracking link in account pages.
     * @return ApiResponse
     */
    public function saveCargoTrackingNumber(
        int $orderId,
        string $kargoTakipNo,
        string $kargoKodu = '',
        string $kargoTakipLink = '',
        string $barkodBilgisi = '',
        bool $kargoTakipLinkGoster = false
    ): ApiResponse {
        $client = $this->request->soap_client($this->apiUrl);
        try {
            $response = $client->__soapCall('SaveKargoTakipNo', [[
                'UyeKodu'              => $this->request->key,
                'siparisId'            => $orderId,
                'kargoKodu'            => $kargoKodu,
                'kargoTakipNo'         => $kargoTakipNo,
                'kargoTakipLink'       => $kargoTakipLink,
                'BarkodBilgisi'        => $barkodBilgisi,
                'KargoTakipLinkGoster' => $kargoTakipLinkGoster,
            ]]);

            return ApiResponse::success($response, 'Cargo tracking number saved successfully.');
        } catch (SoapFault $e) {
            return ApiResponse::error('Error saving cargo tracking number: ' . $e->getMessage());
        }
    }

    private function callSimpleSetter(string $soapMethod, int $orderId): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        try {
            $client->__soapCall($soapMethod, [[
                'UyeKodu'   => $this->request->key,
                'siparisId' => $orderId,
            ]]);

            return ApiResponse::success(null, 'Order updated successfully.');
        } catch (SoapFault $e) {
            return ApiResponse::error('Error calling ' . $soapMethod . ': ' . $e->getMessage());
        }
    }
}
