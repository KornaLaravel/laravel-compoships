<?php

namespace Awobaz\Compoships\Tests\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;

class CodedUser extends Model
{
    use Compoships;

    protected $table = 'coded_users';

    protected $primaryKey = 'code';

    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected $compositeKey = ['code', 'tenant_id'];
}
