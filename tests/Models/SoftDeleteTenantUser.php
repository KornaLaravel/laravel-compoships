<?php

namespace Awobaz\Compoships\Tests\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SoftDeleteTenantUser extends Model
{
    use Compoships;
    use SoftDeletes;

    protected $table = 'soft_delete_tenant_users';

    protected $primaryKey = 'id';

    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected $compositeKey = ['id', 'tenant_id'];
}
