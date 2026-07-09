# OpenCart B2B Supplier Sync Module Demo

Portfolio-ready OpenCart extension demo for **B2B product, price and stock synchronization**.

This repository demonstrates how supplier feeds can be mapped into OpenCart products while keeping the module maintainable, configurable and safe for repeated sync operations.

## What this project demonstrates

- OpenCart admin module structure
- Supplier JSON/XML feed normalization
- SKU-based product upsert logic
- Price and stock update workflow
- Integration logging strategy
- Cron-friendly sync endpoint concept
- Clean separation between controller, model and supplier client
- No real supplier credentials or private client code

## Business use case

B2B and e-commerce companies often need to import thousands of products from supplier feeds. This demo shows a professional way to handle:

- Product matching by SKU
- Supplier SKU mapping
- Stock quantity updates
- Price updates with configurable margin
- Failed item logging
- Manual sync from admin panel
- Cron-based scheduled sync

## Suggested OpenCart installation path

```text
upload/
  admin/controller/extension/module/b2b_sync.php
  admin/language/en-gb/extension/module/b2b_sync.php
  admin/model/extension/module/b2b_sync.php
  admin/view/template/extension/module/b2b_sync.twig
  catalog/controller/extension/module/b2b_sync_cron.php
  catalog/model/extension/module/b2b_supplier_sync.php
  system/library/b2b_sync/supplier_client.php
```

## Demo credentials

This demo intentionally uses `.env.example` and placeholder provider settings. Never commit real supplier tokens.

## Portfolio note

This is a sanitized, non-client demo for GitHub and Upwork portfolio usage.
