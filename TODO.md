# TODO

In-scope topics related to composite-key support in Eloquent that are not yet covered by the package. Each item is a candidate for a future focused change. None of these are blockers for the current feature set.

For the surfaces that ARE covered, see README's "Composite primary keys" section and the relationship documentation above it.

## Polymorphic relations with composite keys

`morphTo`, `morphMany`, and `morphOne` use a `(type_column, id_column)` pair to address the related model. The package does not currently support composite-key columns in either the type/id pair or the related model's primary key. Real feature gap for users with polymorphic data on composite-keyed tables.

Complexity: substantial. Polymorphic resolution is tangled with the discriminator column logic; adding composite-id support requires careful design.

## Through-relations with composite keys

`hasManyThrough` and `hasOneThrough` traverse three tables via two FK relationships. Each leg of the traversal could in principle use a composite key. The package's existing relationship coverage does not include these; behavior is unverified.

Complexity: medium. Both legs need composite handling, plus the intermediate join clause.

## Route model binding by composite key

A URL pattern like `users/{id}/{tenant_id}` cannot bind to a composite primary key model out of the box. Users implement the resolver themselves via `resolveRouteBindingQuery()`. The package could ship a small trait `ResolvesCompositeRouteBindings` that provides a default implementation for composite-key models.

Complexity: small. Roughly 30 lines of code for the trait plus tests.

## Composite find helper

`Model::find($id)` only looks up by the scalar primary key. Users needing to find by composite key write `Model::where(...)->where(...)->first()` every time. A `findComposite([$id, $tenant_id])` (or keyed-array variant) helper on the trait collapses the boilerplate.

Complexity: small. Roughly 15 lines of code for the trait method plus tests.

## Broadcasting channels keyed by composite identity

`Broadcast::channel('App.Models.User.{id}.{tenant_id}', ...)` works in principle but the channel-name construction is user-side string building. The package could ship a helper that produces a canonical composite channel suffix and a matching resolver. Lower priority since users can build channel names directly.

Complexity: small. Mostly documentation plus a helper method.

## Cast types beyond BackedEnum on composite columns

The trait's null and raw-original handling should round-trip cleanly through datetime, encrypted, and custom Cast classes on composite-key columns. Behavior is unverified by tests. The save and queue paths both use values that flow through the cast layer; round-trip should work as long as the cast is deterministic, but no test pins this.

Complexity: small. Add fixtures with cast columns of the relevant types and exercise the existing test patterns.

## What is already covered

For context, the package already supports composite keys on:

- `hasOne`, `hasMany`, `belongsTo`, `belongsToMany` relationships (custom pivot models, asymmetric scalar/composite pairs, eager loading via tuple `whereIn`, `withPivot` and `withTimestamps`)
- `save`, `update`, `delete`, `refresh`, `fresh` write path on models declaring `$compositeKey`
- Soft deletes (delete, forceDelete, restore) on composite-keyed models
- Queue serialization of single composite-keyed model properties via `SerializesModels` (JSON-encoded ids and OR-of-AND restoration)
- Queue serialization of collections via the `QueueableCompositeCollection` wrapper

See README's "Composite primary keys" section for usage.
