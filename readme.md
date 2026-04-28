# Ticimax PHP SDK

A modern PHP SDK for interacting with Ticimax E-commerce Web Services. Built with modern PHP practices, strict typing, and clean architecture.

## Features

- 🚀 Modern PHP 7.4+ with strict typing
- 🎯 PSR-4 autoloading compliant
- 🔄 Comprehensive API service coverage
- 🛡️ Robust error handling and responses
- 📦 Consistent model structure
- 🔧 Easy configuration and setup

## Installation

```bash
composer require alperragib/ticimax-php-service
```

## Quick Start

```php
use AlperRagib\Ticimax\Ticimax;

// Initialize the client
$ticimax = new Ticimax('https://your-store.com', 'your-api-key');

// Get product service
$productService = $ticimax->productService();

// Fetch products with filters
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

```

## Available Services

- 🛍️ Products (`ProductService`)
  - Product management
  - Variations
  - Favorite products
- 📂 Categories (`CategoryService`)
- 🏢 Brands (`BrandService`)
- 📦 Orders (`OrderService`)
- 👥 Users (`UserService`)
- 📍 Locations (`LocationService`)
- 🏪 Suppliers (`SupplierService`)
- 📋 Menus (`MenuService`)

## Detailed Usage

### User Operations

```php
// User authentication
$userService = $ticimax->userService();
$loginResponse = $userService->login('user@example.com', 'password');
```

### Order Operations

The `OrderService` covers the order lifecycle methods that Ticimax's
`SiparisServis.svc` exposes. Note that Ticimax does **not** provide a generic
"update order" method — once an order is created you can mutate its **status**,
its **invoice number**, its **cargo tracking info**, and the
**integration-transferred flag**, but not its line items, addresses, or totals.
For those, use Ticimax's admin panel.

```php
use AlperRagib\Ticimax\Service\Order\OrderStatus;
use AlperRagib\Ticimax\Service\Order\PaymentStatus;
use AlperRagib\Ticimax\Service\Order\PaymentType;

$orderService = $ticimax->orderService();

// 1) Create an order with payment marked as "awaiting approval"
$created = $orderService->createOrder([
    /* ... other fields ... */
    'Odeme' => [
        'OdemeDurumu'    => PaymentStatus::ONAY_BEKLIYOR, // 0
        'OdemeTipi'      => PaymentType::HAVALE,          // 1
        'OdemeSecenekID' => 1,
        'TaksitSayisi'   => 1,
        'Tarih'          => date('c'),
        'Tutar'          => 100.00,
    ],
]);
$orderId = (int) $created->data->ID;

// 2) Move the order into "Ödeme bekliyor" so the customer/back office sees it
$orderService->setOrderStatus($orderId, OrderStatus::ODEME_BEKLIYOR);

// 3) When the payment provider (PayTR / iyzico) confirms the payment via its
//    notify webhook, flip BOTH the order status AND the payment record:
$orderService->setOrderStatus(
    $orderId,
    OrderStatus::ONAYLANDI,
    '',     // optional cargo tracking number
    true    // notify the customer by email
);

$paymentId = $orderService->getOrderPaymentId($orderId);
if ($paymentId !== null) {
    $orderService->setOrderPaymentStatus($orderId, $paymentId, PaymentStatus::ONAYLANDI);
}

// Cargo / fulfillment shortcuts
$orderService->saveCargoTrackingNumber($orderId, '4561562545', '', 'https://carrier.example/track/4561562545');
$orderService->setOrderShipped($orderId);     // OrderStatus::KARGOYA_VERILDI
$orderService->setOrderDelivered($orderId);   // OrderStatus::TESLIM_EDILDI
$orderService->setInvoiceNumber($orderId, 'FTR-2025-0001');
```

#### `OrderService` methods

| Method                                                                    | Ticimax SOAP call          | What it does                                                   |
| ------------------------------------------------------------------------- | -------------------------- | -------------------------------------------------------------- |
| `getOrders($filters, $pagination)`                                        | `SelectSiparis`            | List orders.                                                   |
| `createOrder($order)`                                                     | `SaveSiparis`              | Create a new order.                                            |
| `setOrderStatus($id, $status, $kargoTakipNo = '', $notifyByMail = false)` | `SetSiparisDurum`          | Change the order's status. Use the `OrderStatus::*` constants. |
| `setOrderPaymentStatus($id, $odemeId, $status, $notifyByMail = false)`    | `SetSiparisOdemeDurum`     | Change a payment record's status (e.g. on PayTR / iyzico notify). Use `PaymentStatus::*`. |
| `getOrderPaymentId($id)`                                                  | `SelectSiparis`            | Helper: look up `WebSiparisOdeme.ID` for an order — needed for `setOrderPaymentStatus`. |
| `setOrderShipped($id)`                                                    | `SetSiparisKargoyaVerildi` | Shortcut for the "Kargoya verildi" state.                      |
| `setOrderDelivered($id)`                                                  | `SetSiparisTeslimEdildi`   | Shortcut for the "Teslim edildi" state.                        |
| `setOrderTransferred($id)`                                                | `SetSiparisAktarildi`      | Mark the order as transferred to an external system.           |
| `unsetOrderTransferred($id)`                                              | `SetSiparisAktarildiIptal` | Cancel the "transferred" flag.                                 |
| `setInvoiceNumber($id, $invoiceNo)`                                       | `SetFaturaNo`              | Attach an invoice number to the order.                         |
| `saveCargoTrackingNumber($id, $trackingNo, ...)`                          | `SaveKargoTakipNo`         | Save / update the cargo tracking number.                       |

#### Enum constants

The `AlperRagib\Ticimax\Service\Order\OrderStatus`,
`PaymentStatus`, and `PaymentType` classes expose the integer values that
Ticimax expects:

**`OrderStatus`** integer constants. Pass them straight to `setOrderStatus`; the
service converts them to the PascalCase string the WSDL's `WebSiparisDurumlari`
enum expects (e.g. `ODEME_BEKLIYOR` → `"OdemeBekliyor"`). You can also pass the
PascalCase string directly if you prefer.

| Constant              | Value | Meaning                      |
| --------------------- | ----- | ---------------------------- |
| `ON_SIPARIS`          | 0     | Ön sipariş                   |
| `ONAY_BEKLIYOR`       | 1     | Onay bekliyor                |
| `ONAYLANDI`           | 2     | Onaylandı                    |
| `ODEME_BEKLIYOR`      | 3     | Ödeme bekliyor               |
| `PAKETLENIYOR`        | 4     | Paketleniyor                 |
| `TEDARIK_EDILIYOR`    | 5     | Tedarik ediliyor             |
| `KARGOYA_VERILDI`     | 6     | Kargoya verildi              |
| `TESLIM_EDILDI`       | 7     | Teslim edildi                |
| `IPTAL`               | 8     | İptal                        |
| `IADE`                | 9     | İade                         |
| `SILINMIS`            | 10    | Silinmiş                     |
| `IADE_TALEP_ALINDI`   | 11    | İade talebi alındı           |
| `IADE_ULASTI_ODEME`   | 12    | İade ulaştı, ödeme yapılacak |
| `IADE_ODEME_YAPILDI`  | 13    | İade ödemesi yapıldı         |
| `TESLIM_ONCESI_IPTAL` | 14    | Teslimat öncesi iptal        |
| `IPTAL_TALEBI`        | 15    | İptal talebi                 |
| `KISMI_IADE_TALEBI`   | 16    | Kısmi iade talebi            |
| `KISMI_IADE_YAPILDI`  | 17    | Kısmi iade yapıldı           |
| `TESLIM_EDILEMEDI`    | 18    | Teslim edilemedi             |

**`PaymentStatus`** integer constants. Used both in `Odeme.OdemeDurumu` when
creating an order, and as the `$status` argument to `setOrderPaymentStatus()`
(the service translates them to the PascalCase strings `WebOdemeDurumlari`
expects, e.g. `ONAYLANDI` → `"Onaylandi"`):

| Constant         | Value | Meaning       |
| ---------------- | ----- | ------------- |
| `ONAY_BEKLIYOR`  | 0     | Onay bekliyor |
| `ONAYLANDI`      | 1     | Onaylandı     |
| `HATALI`         | 2     | Hatalı        |
| `IADE_EDILMIS`   | 3     | İade edilmiş  |
| `IPTAL_EDILMIS`  | 4     | İptal edilmiş |

**`PaymentType`** (used in `Odeme.OdemeTipi`): `KREDI_KARTI=0`, `HAVALE=1`,
`KAPIDA_ODEME_NAKIT=2`, `KAPIDA_ODEME_KK=3`, `MOBIL_ODEME=4`, `BKM_EXPRESS=5`,
`PAYPAL=6`, `CARI=7`, `MAIL_ORDER=8`, `IPARA=9`, `NAKIT=10`, `PAYUONECLICK=11`,
`CARI_KREDI=12`, `GARANTIPAY=13`, `PAYU_BKMEXPRESS=14`, `NESTPAY=15`,
`PAYCELL=16`, `IYZIPAY=17`, `HOPI=18`, `PAYBYME=19`, `HEDIYE_CEKI=20`,
`PAYGURUMOBIL=21`, `PAYNET=22`, `TELR=23`, `COMPAY=24`, `PAYTR=25`,
`MAXIMUM_MOBIL=26`, `MAGAZADA_ODE=27`.

> **Note on payment vs. order status.** Ticimax keeps two separate fields: the
> **order status** (`OrderStatus`) drives the order's lifecycle (Ödeme bekliyor
> → Onaylandı → Kargoya verildi → …), while **payment status**
> (`PaymentStatus`) describes the WebSiparisOdeme row's own state. PayTR /
> iyzico notify callbacks should typically flip BOTH:
> `setOrderStatus(..., OrderStatus::ONAYLANDI)` so the order moves out of
> "Ödeme bekliyor", **and** `setOrderPaymentStatus(..., PaymentStatus::ONAYLANDI)`
> so the admin panel shows the payment row as captured. Setting only the order
> status leaves the payment record looking unpaid.

## Error Handling

The SDK uses a consistent response structure through the `ApiResponse` class:

```php
if ($response->isSuccess()) {
    $data = $response->getData();
} else {
    echo "Error: " . $response->getMessage();
}
```

## Configuration

Create a configuration file:

```php
// config.php
define('TICIMAX_MAIN_DOMAIN', 'https://your-store.com');
define('TICIMAX_API_KEY', 'your-api-key');
```

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

- Check the [examples](example/) directory for more usage examples
- Submit issues through GitHub
- Follow PSR-12 coding standards when contributing

## Requirements

- PHP 7.4 or higher
- SOAP extension
- JSON extension
- Composer
