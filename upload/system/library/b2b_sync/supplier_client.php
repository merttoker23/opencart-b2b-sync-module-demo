<?php

class SupplierClient
{
    public function fetchProducts(string $feedUrl): array
    {
        // This demo returns static data. Production implementation should use cURL/Guzzle with timeouts.
        return [
            [
                'sku' => 'B2B-DEMO-001',
                'name' => 'B2B Demo Product',
                'description' => 'Sample supplier product for sync demo.',
                'price' => 29.90,
                'stock' => 75,
                'active' => true,
            ],
            [
                'sku' => 'B2B-DEMO-002',
                'name' => 'B2B Demo Accessory',
                'description' => 'Second sample product for sync demo.',
                'price' => 9.90,
                'stock' => 200,
                'active' => true,
            ],
        ];
    }
}
