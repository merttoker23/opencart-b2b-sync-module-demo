<?php

class ModelExtensionModuleB2BSync extends Model
{
    public function syncProducts(): array
    {
        $this->load->model('catalog/product');
        $this->load->library('b2b_sync/supplier_client');

        $feedUrl = $this->config->get('module_b2b_sync_feed_url');
        $margin = (float) $this->config->get('module_b2b_sync_margin');
        $items = $this->supplier_client->fetchProducts($feedUrl);

        $created = 0;
        $updated = 0;
        $failed = [];

        foreach ($items as $item) {
            try {
                $normalized = $this->normalizeProduct($item, $margin);
                $productId = $this->findProductIdBySku($normalized['sku']);

                if ($productId) {
                    $this->updateProduct($productId, $normalized);
                    $updated++;
                } else {
                    $this->createProduct($normalized);
                    $created++;
                }
            } catch (Throwable $exception) {
                $failed[] = [
                    'sku' => $item['sku'] ?? null,
                    'error' => $exception->getMessage(),
                ];
            }
        }

        $this->writeLog('supplier_sync', compact('created', 'updated', 'failed'));

        return compact('created', 'updated', 'failed');
    }

    private function normalizeProduct(array $item, float $margin): array
    {
        $basePrice = (float) $item['price'];
        $price = round($basePrice + ($basePrice * $margin / 100), 2);

        return [
            'sku' => trim($item['sku']),
            'name' => trim($item['name']),
            'description' => $item['description'] ?? '',
            'quantity' => (int) $item['stock'],
            'price' => $price,
            'status' => !empty($item['active']) ? 1 : 0,
        ];
    }

    private function findProductIdBySku(string $sku): ?int
    {
        $query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE sku = '" . $this->db->escape($sku) . "' LIMIT 1");
        return $query->num_rows ? (int) $query->row['product_id'] : null;
    }

    private function createProduct(array $data): void
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "product SET sku='" . $this->db->escape($data['sku']) . "', quantity='" . (int)$data['quantity'] . "', price='" . (float)$data['price'] . "', status='" . (int)$data['status'] . "', date_added=NOW(), date_modified=NOW()");
        $productId = (int) $this->db->getLastId();
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id='" . $productId . "', language_id='1', name='" . $this->db->escape($data['name']) . "', description='" . $this->db->escape($data['description']) . "'");
    }

    private function updateProduct(int $productId, array $data): void
    {
        $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity='" . (int)$data['quantity'] . "', price='" . (float)$data['price'] . "', status='" . (int)$data['status'] . "', date_modified=NOW() WHERE product_id='" . $productId . "'");
        $this->db->query("UPDATE " . DB_PREFIX . "product_description SET name='" . $this->db->escape($data['name']) . "', description='" . $this->db->escape($data['description']) . "' WHERE product_id='" . $productId . "' AND language_id='1'");
    }

    private function writeLog(string $type, array $payload): void
    {
        $this->log->write('[B2B Sync][' . $type . '] ' . json_encode($payload));
    }
}
