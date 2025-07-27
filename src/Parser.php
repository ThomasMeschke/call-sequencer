<?php

declare(strict_types=1);

namespace thomas\cseq;

use Exception;

class Parser
{
    private Call $allCalls;

    public function __construct()
    {
        $this->allCalls = new Call(CallType::Virtual, '{all}', '', 0, null);
    }

    /**
     * @param array<array<string>> $callstacks
     */
    public function parse(array $callstacks): Call
    {
        foreach ($callstacks as $callstack) {
            $this->processCallstack($callstack, $this->allCalls, 0);
        }

        $this->allCalls->selfFinalize();
        return $this->allCalls;
    }

    /**
     * @param array<string> $callstack
     */
    private function processCallstack(array $callstack, ?Call $caller, int $stackDepth): void
    {
        if (empty($callstack)) {
            return;
        }

        $currentCallString = array_shift($callstack);
        $currentCall = $this->buildCall($currentCallString, $caller, $stackDepth + 1);

        if (null !== $caller && $currentCall->equals($caller->lastCallee())) {
            if (null !== $caller->lastCallee() && ! $caller->lastCallee()->isFinalized()) {
                if ($currentCall->isFinalized()) {
                    $caller->lastCallee()->finalize((int)$currentCall->getCost());
                }
                $currentCall = $caller->lastCallee();
            } else {
                $caller->addCallee($currentCall);
            }
        } else {
            $caller?->addCallee($currentCall);
        }

        $this->processCallstack($callstack, $currentCall, $stackDepth + 1);
    }

    private function buildCall(string $callString, ?Call $caller, int $stackDepth): Call
    {
        $segments = explode(' ', $callString);

        $callTarget = $segments[0];
        $callDuration = $segments[1] ?? null;

        $call = CallBuilder::fromCaller($caller)
                            ->targeting($callTarget)
                            ->atStackDepth($stackDepth)
                            ->build();

        if ($callDuration !== null) {
            $call->finalize((int) $callDuration);
        }

        return $call;
    }
}
