# Supplier Sync Flow

```text
Admin / Cron
    ↓
B2B Sync Controller
    ↓
Supplier Client
    ↓
Normalize product payload
    ↓
Find product by SKU
    ↓
Create or update product
    ↓
Write integration log
```

## Production checklist

- Add chunking for large feeds.
- Store last sync cursor or timestamp.
- Add retry and timeout controls.
- Track failed items separately.
- Add admin report for sync logs.
- Avoid overwriting manually managed fields unless configured.
