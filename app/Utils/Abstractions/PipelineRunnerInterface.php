<?php

namespace App\Utils\Abstractions;

use BMCLibrary\Utils\Result;

interface PipelineRunnerInterface
{
    public function run(iterable $steps, array &$context): Result;
}
