<?php

namespace Awobaz\Compoships\Tests\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;

class TenantUserNote extends Model
{
    use Compoships;

    protected $table = 'tenant_user_notes';

    public $timestamps = false;

    protected $guarded = [];
}
