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
