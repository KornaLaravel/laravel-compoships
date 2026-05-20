<?php

namespace Awobaz\Compoships\Tests\Models;

use Awobaz\Compoships\Compoships;
use Awobaz\Compoships\Tests\Enums\TenantEnum;
use Illuminate\Database\Eloquent\Model;

class EnumTenantUser extends Model
{
    use Compoships;

    protected $table = 'enum_tenant_users';

    protected $primaryKey = 'id';

    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected $compositeKey = ['id', 'tenant_id'];

    protected $casts = [
        'tenant_id' => TenantEnum::class,
    ];
}
