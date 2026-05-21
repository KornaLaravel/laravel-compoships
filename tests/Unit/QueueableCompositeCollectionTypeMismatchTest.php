<?php

namespace Awobaz\Compoships\Tests\Unit;

use Awobaz\Compoships\Queue\QueueableCompositeCollection;
use Awobaz\Compoships\Tests\Models\TenantUser;
use Awobaz\Compoships\Tests\TestCase\TestCase;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Collection;

class QueueableCompositeCollectionTypeMismatchTest extends TestCase
{
    public function test_type_mismatch()
    {
        Capsule::table('tenant_users')->insert([
            ['id' => 'u1', 'tenant_id' => '1', 'name' => 'Alice'],
        ]);

        // simulate model at wrap time with integer tenant_id
        $model = new TenantUser();
        $model->id = 'u1';
        $model->tenant_id = 1;
        $model->name = 'Alice';
        $model->exists = true;
        // make sure tenant_id is not in $casts
        
        $collection = new Collection([$model]);
        $bag = QueueableCompositeCollection::for($collection);

        $restored = $bag->restore();
        
        $this->assertCount(1, $restored);
    }
}
