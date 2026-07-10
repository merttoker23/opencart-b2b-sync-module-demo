# OpenCart B2B Supplier Sync Module

[![PHP lint](https://github.com/merttoker23/opencart-b2b-sync-module-demo/actions/workflows/php-lint.yml/badge.svg)](https://github.com/merttoker23/opencart-b2b-sync-module-demo/actions/workflows/php-lint.yml)

A sanitized OpenCart extension reference for **B2B product, price and stock synchronization**.

The module shows how an external supplier feed can be normalized and mapped into OpenCart while keeping repeated imports safe, operationally visible and separated from controller code. It contains no real supplier credentials, client code or production data.

## Business problem

B2B and e-commerce teams often need to import large supplier catalogs while preserving local rules. A reliable synchronization flow must handle:

- SKU-based product matching
- supplier SKU and field mapping
- stock and price updates
- configurable price margins
- disabled or unavailable products
- manual and scheduled execution
- partial failures without losing the complete run result
- traceable logs for support and operations

## Module structure

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

## Synchronization flow

```text
Admin action or scheduled request
  -> authorization / cron-token validation
  -> supplier client
  -> provider payload normalization
  -> product lookup by SKU
  -> create or update product
  -> collect created, updated and failed counts
  -> write an operational log entry
```

Detailed design: [Supplier synchronization design](docs/sync-flow.md)

## Security decisions

- The admin sync action checks OpenCart `modify` permission before executing.
- The scheduled endpoint rejects empty configuration and compares the cron token with `hash_equals`.
- Example credentials are placeholders only.
- Production logs should never include authorization headers, supplier tokens or full personal data.

## Repeated execution and idempotency

Products are matched by SKU so the same supplier item is updated rather than inserted repeatedly. A production implementation should also enforce unique SKU constraints, store provider cursors or timestamps and prevent overlapping synchronization runs.

## Failure handling

The example collects failed items instead of aborting the full feed after the first invalid record. In production, failures should be stored in a dedicated table with a sanitized reason, retry status and correlation identifier.

## Production extension path

For high-volume catalogs, the synchronous loop should move to a queue or chunked CLI job. Recommended additions include:

- connection and response timeouts;
- bounded retries with backoff;
- pagination or streaming feed readers;
- distributed locking for overlapping runs;
- batch-level and item-level metrics;
- dry-run mode and field-level overwrite configuration;
- tests for invalid payloads, duplicate SKUs and partial provider failures.

See [Production readiness checklist](docs/production-readiness.md).

## Local review

The repository is a module reference rather than a full OpenCart distribution. PHP files can be checked independently:

```bash
find upload -type f -name '*.php' -print0 | xargs -0 -n1 php -l
```

GitHub Actions performs this syntax check on pushes and pull requests.

## Review focus

When evaluating changes to this type of integration, the main concerns are authorization, provider timeouts, field mapping, idempotency, stock correctness, overlapping runs, secret handling and operational traceability.
