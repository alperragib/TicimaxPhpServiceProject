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
    'Aktif' => 1,
    'KategoriID' => 0
];

$pagination = [
    'KayitSayisi' => 20,
    'SayfaNo' => 1
];

$response = $productService->getProducts($filters, $pagination);

if ($response->isSuccess()) {
    foreach ($response->getData() as $product) {
        echo $product->UrunAdi . "\n";
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

// Get user addresses
$addresses = $userService->getUserAddresses(1050);
```

### Order Management

```php
// Fetch orders
$orderService = $ticimax->orderService();
$orders = $orderService->getOrders([
    'SiparisTarihiBas' => '2024-01-01',
    'SiparisTarihiSon' => '2025-01-01'
]);
```

### Product Management

```php
// Get product variations
$productService = $ticimax->productService();
$variations = $productService->getProductVariations([
    'Aktif' => 1,
    'UrunKartiID' => 123
]);
```

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
