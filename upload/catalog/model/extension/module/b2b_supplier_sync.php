<?php

class ModelExtensionModuleB2BSupplierSync extends Model
{
    public function syncProducts(): array
    {
        // In a production module, shared logic can be moved into a service class.
        $this->log->write('[B2B Sync] Cron sync triggered.');
        return ['created' => 0, 'updated' => 0, 'message' => 'Cron endpoint demo.'];
    }
}
