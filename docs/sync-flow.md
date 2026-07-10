# Supplier Synchronization Design

## Flow

```text
Admin action or scheduled request
  -> permission / token validation
  -> supplier client
  -> payload validation and normalization
  -> product lookup by SKU
  -> create or update product fields
  -> collect created, updated and failed counters
  -> write operational log data
```

## Boundary responsibilities

### Controller

The controller authenticates the caller, invokes the synchronization use case and returns a stable response. It should not contain provider mapping or product update rules.

### Supplier client

The provider client owns transport details such as URL construction, authentication headers, connection timeouts, response parsing and provider-specific errors.

### Normalization

Supplier fields are converted into an internal product structure before database writes. Required fields such as SKU and name should be validated. Price and stock values must be converted explicitly rather than relying on loose PHP casting.

### Persistence

SKU is the business key for this example. Production systems should back this assumption with a unique constraint or a dedicated supplier-product mapping table. Local fields should only be overwritten when configuration permits it.

## Failure model

One invalid product should not hide the outcome of the entire run. Each failed item should record a sanitized reason and identifier while successful items continue. Transport-level failures may require retrying the batch; item-level validation failures usually require data correction instead.

## Concurrency

Scheduled and manual runs can overlap. A production module should use a lock with a clear expiration policy so two workers cannot update the same catalog simultaneously.

## Observability

Useful run-level information includes start/end time, provider, processed count, created count, updated count, failed count and duration. Item-level failures should be searchable without storing secrets or unnecessary personal information.