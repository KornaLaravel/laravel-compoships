<?php

namespace Awobaz\Compoships\Tests\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;

class TenantUser extends Model
{
    use Compoships;

    protected $table = 'tenant_users';

    protected $primaryKey = 'id';

    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected $compositeKey = ['id', 'tenant_id'];

    public function notes()
    {
        return $this->hasMany(
            TenantUserNote::class,
            ['tenant_user_id', 'tenant_id'],
            ['id', 'tenant_id']
        );
    }
}
