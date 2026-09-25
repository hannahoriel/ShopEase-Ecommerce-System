<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$product = App\Models\Seller\Product::with(['options', 'productSpecifications'])->first();
if (!$product) {
    echo "NO_PRODUCT\n";
    exit(0);
}
echo $product->name . "\n";
var_export($product->variations); echo "\n";
var_export($product->colors); echo "\n";
var_export($product->sizes); echo "\n";
var_export($product->specifications); echo "\n";
echo 'options=' . count($product->options) . "\n";
echo 'specs=' . count($product->productSpecifications) . "\n";
