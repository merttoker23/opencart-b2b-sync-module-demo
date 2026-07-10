# Production Readiness Checklist

## Access control

- Require OpenCart admin `modify` permission for manual execution.
- Use a long random scheduled-task token and compare it with `hash_equals`.
- Prefer a CLI command or protected internal scheduler over a public HTTP cron endpoint when possible.
- Rotate credentials and keep them outside the repository.

## Provider communication

- Configure connection and response timeouts.
- Retry only transient failures and use bounded exponential backoff.
- Respect provider rate limits and pagination.
- Validate HTTP status, content type and response schema.

## Product mapping

- Validate required fields before database writes.
- Define currency, tax and rounding rules explicitly.
- Decide which locally managed fields may be overwritten.
- Use a supplier mapping table when one local product can have multiple provider identifiers.

## Data integrity

- Enforce unique business keys in the database.
- Prevent overlapping runs with a lock.
- Make repeated processing idempotent.
- Use transactions where multiple related tables must change together.

## Scale

- Stream or paginate large feeds instead of loading them fully into memory.
- Process records in chunks or queued jobs.
- Save progress so interrupted runs can resume safely.
- Separate retryable transport errors from invalid product data.

## Operations

- Store run summaries and sanitized item failures.
- Add correlation identifiers to logs.
- Measure duration, processed records and failure rate.
- Alert on repeated provider failures or unusual stock changes.
- Provide dry-run and rollback procedures for risky mapping changes.

## Tests

- New and existing SKU behavior
- Duplicate supplier records
- Missing and malformed fields
- Price rounding and margin rules
- Zero and negative stock
- Provider timeout and invalid JSON/XML
- Permission and cron-token failures
- Overlapping synchronization attempts
