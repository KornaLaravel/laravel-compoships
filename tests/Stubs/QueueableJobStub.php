<?php

namespace Awobaz\Compoships\Tests\Stubs;

use Illuminate\Queue\SerializesModels;

class QueueableJobStub
{
    use SerializesModels;

    public $model;

    public function __construct($model)
    {
        $this->model = $model;
    }
}
