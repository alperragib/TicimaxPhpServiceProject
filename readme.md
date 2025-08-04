# Ticimax Web Services PHP Library

## Professional Structure (2024+)

This library provides a modern, professional, and consistent PHP interface for Ticimax Web Services, using strict types, PSR standards, and a clear separation of models and services.

---

## Usage Example

```php
use AlperRagib\Ticimax\TicimaxRequest;
use AlperRagib\Ticimax\Service\Product\ProductService;
use AlperRagib\Ticimax\Model\Product\ProductModel;
use AlperRagib\Ticimax\TicimaxHelpers;

// Setup
$mainDomain = 'https://your-ticimax-domain.com';
$apiKey = 'YOUR_API_KEY';
$request = new TicimaxRequest($mainDomain, $apiKey);
$productService = new ProductService($request);

// Fetch products
$products = $productService->getProducts();
foreach ($products as $product) {
    echo $product->getName() . PHP_EOL;
}

// Create a new product
$helper = new TicimaxHelpers();
$newProduct = new ProductModel($helper, [
    'name' => 'New Product',
    'isActive' => true,
    // ... other fields
]);
$success = $productService->createProduct($newProduct);
echo $success ? 'Product created!' : 'Failed to create product.';
```

---

## Migration Note

- **All models and services have been refactored and moved to `src/Model/` and `src/Service/` respectively.**
- **Old files are deprecated and will be removed in a future release.**
- **Please update your code to use the new structure.**

---

## Features
- PSR-4 autoloading, strict types, and modern PHP best practices
- Consistent models for Product, ProductVariation, Brand, Category, Supplier, Order
- Service classes for all major domains
- Easy to extend and maintain

---

## Contributing
Pull requests are welcome! Please follow PSR-12 and add tests for new features.
