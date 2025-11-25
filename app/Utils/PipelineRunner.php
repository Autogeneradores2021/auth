<?php

namespace App\Utils;

use App\Utils\Abstractions\PipelineRunnerInterface;
use BMCLibrary\Utils\Result;

class PipelineRunner implements PipelineRunnerInterface
{
    /**
     * Ejecuta pasos secuenciales. Cada paso recibe el contexto por referencia y
     * debe devolver null (continuar) o Result (fallo o éxito final).
     *
     * @param iterable $steps  array|iterable of callables (fn(array& $ctx): ?Result)
     * @param array $context   contexto compartido por pasos
     * @return Result
     */
    public function run(iterable $steps, array &$context): Result
    {
        foreach ($steps as $step) {
            $res = $step($context);
            if ($res instanceof Result) {
                return $res;
            }
        }

        // Si ningún paso devolvió Result, se considera fallo genérico
        return Result::fail(error: 'Validation pipeline did not return a result');
    }
}
