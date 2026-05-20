<?php

namespace Awobaz\Compoships;

use Awobaz\Compoships\Database\Eloquent\Concerns\HasRelationships;
use Awobaz\Compoships\Database\Grammar\MariaDbGrammar;
use Awobaz\Compoships\Database\Grammar\MySqlGrammar;
use Awobaz\Compoships\Database\Grammar\PostgresGrammar;
use Awobaz\Compoships\Database\Grammar\SQLiteGrammar;
use Awobaz\Compoships\Database\Grammar\SqlServerGrammar;
use Awobaz\Compoships\Database\Query\Builder as QueryBuilder;
use Awobaz\Compoships\Exceptions\InvalidUsageException;
use Illuminate\Support\Str;

trait Compoships
{
    use HasRelationships;

    public function getAttribute($key)
    {
        if (is_array($key)) { //Check for multi-columns relationship
            return array_map(fn ($k) => parent::getAttribute($k), $key);
        }

        return parent::getAttribute($key);
    }

    public function qualifyColumn($column)
    {
        if (is_array($column)) {
            return array_map(function ($c) {
                if (Str::contains($c, '.')) {
                    return $c;
                }

                $connection = $this->getConnection();
                $prefix = $connection->getTablePrefix();

                return $prefix.$this->getTable().'.'.$c;
            }, $column);
        }

        return parent::qualifyColumn($column);
    }

    /**
     * Composite-key columns to AND into the save/select WHERE clause,
     * excluding the scalar primary key (already added by parent).
     *
     * Consumers opt in by declaring `protected $compositeKey = [...]`
     * on the consuming model. The property is intentionally NOT declared
     * on the trait so consumers can pick their own default value without
     * triggering PHP's "trait property re-declaration" incompatibility.
     *
     * @return array<int, string>
     *
     * @throws \Awobaz\Compoships\Exceptions\InvalidUsageException
     */
    protected function getAdditionalKeyNames()
    {
        if (! property_exists($this, 'compositeKey') || empty($this->compositeKey)) {
            return [];
        }

        if (! in_array($this->getKeyName(), $this->compositeKey, true)) {
            throw new InvalidUsageException(sprintf(
                'Model %s declares $compositeKey but does not include the scalar primary key "%s". Add it to $compositeKey or remove $compositeKey entirely.',
                static::class,
                $this->getKeyName()
            ));
        }

        return array_values(array_diff($this->compositeKey, [$this->getKeyName()]));
    }

    /**
     * Set the keys for a save UPDATE / DELETE query, including any
     * additional composite-key columns declared via $compositeKey.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @throws \Awobaz\Compoships\Exceptions\InvalidUsageException
     */
    protected function setKeysForSaveQuery($query)
    {
        $query = parent::setKeysForSaveQuery($query);

        foreach ($this->getAdditionalKeyNames() as $column) {
            $value = array_key_exists($column, $this->original)
                ? $this->original[$column]
                : $this->getAttribute($column);

            $value === null
                ? $query->whereNull($column)
                : $query->where($column, '=', $value);
        }

        return $query;
    }

    /**
     * Set the keys for a SELECT query used by fresh() / refresh(),
     * including any additional composite-key columns declared via
     * $compositeKey.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @throws \Awobaz\Compoships\Exceptions\InvalidUsageException
     */
    protected function setKeysForSelectQuery($query)
    {
        $query = parent::setKeysForSelectQuery($query);

        foreach ($this->getAdditionalKeyNames() as $column) {
            $value = array_key_exists($column, $this->original)
                ? $this->original[$column]
                : $this->getAttribute($column);

            $value === null
                ? $query->whereNull($column)
                : $query->where($column, '=', $value);
        }

        return $query;
    }

    /**
     * Configure Eloquent to use Compoships Query Builder.
     *
     * @return \Awobaz\Compoships\Database\Query\Builder|static
     */
    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();

        $grammar = match ($connection->getDriverName()) {
            'mysql'   => new MySqlGrammar($connection),
            'pgsql'   => new PostgresGrammar($connection),
            'sqlite'  => new SQLiteGrammar($connection),
            'sqlsrv'  => new SqlServerGrammar($connection),
            'mariadb' => new MariaDbGrammar($connection),
            default   => $connection->getQueryGrammar(),
        };

        if (method_exists($grammar, 'setConnection')) {
            $grammar->setConnection($connection);
        }

        if (method_exists($connection, 'withTablePrefix')) {
            $grammar = $connection->withTablePrefix($grammar);
        }

        return new QueryBuilder($connection, $grammar, $connection->getPostProcessor());
    }
}
