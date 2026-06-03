<?php

namespace Awobaz\Compoships\Tests\Stubs;

use Illuminate\Queue\SerializesModels;

class QueueableJobStub
{
    use SerializesModels;

    public $model;

    public $secondModel;

    public function __construct($model, $secondModel = null)
    {
        $this->model = $model;
        $this->secondModel = $secondModel;
    }
}
