<?php

declare(strict_types=1);

namespace thomas\cseq;

use Exception;

class CallBuilder
{
    private ?Call $caller;
    private string $callTarget;
    private int $stackDepth;

    private function __construct()
    {
    }

    public static function fromCaller(?Call $caller): CallBuilder
    {
        $callBuilder = new CallBuilder();
        $callBuilder->caller = $caller;
        return $callBuilder;
    }

    public function targeting(string $callTarget): CallBuilder
    {
        $this->callTarget = $callTarget;
        return $this;
    }

    public function atStackDepth(int $stackDepth): CallBuilder
    {
        $this->stackDepth = $stackDepth;
        return $this;
    }

    public function build(): Call
    {
        if (self::isStdLibCall()) {
            return self::buildStdLibCall();
        }
        if (self::isGlobalCall()) {
            return self::buildGlobalCall();
        }
        if (self::isLanguageConstructCall()) {
            return self::buildLanguageConstructCall();
        }
        if (self::isNativeCall()) {
            return self::buildNativeCall();
        }

        [$class, $function] = self::splitUp();
        if (self::isInstanceMethodCall()) {
            return self::buildInstanceCall($class, $function);
        }
        if (self::isStaticMethodCall()) {
            return self::buildStaticCall($class, $function);
        }

        throw new Exception("Cannot determine call type of '{$this->callTarget}'");
    }

    private function buildStdLibCall(): Call
    {
        return new Call(CallType::StandardLibrary, 'StdLib', $this->callTarget, $this->stackDepth, $this->caller);
    }

    private function buildGlobalCall(): Call
    {
        return new Call(CallType::Global, 'Global', $this->callTarget, $this->stackDepth, $this->caller);
    }

    private function buildLanguageConstructCall(): Call
    {
        $constructBegin = (int)strpos($this->callTarget, '{');
        $constructEnd = (int)strpos($this->callTarget, '}');
        $constructLength = ($constructEnd - $constructBegin) + 1;

        $construct = substr($this->callTarget, $constructBegin, $constructLength);

        return new Call(CallType::LanguageConstruct, 'Native', $construct, $this->stackDepth, $this->caller);
    }

    private function buildNativeCall(): Call
    {
        return new Call(CallType::Native, $this->callTarget, '', $this->stackDepth, $this->caller);
    }

    private function buildStaticCall(string $class, string $function): Call
    {
        return new Call(CallType::StaticMethod, $class, $function, $this->stackDepth, $this->caller);
    }

    private function buildInstanceCall(string $class, string $function): Call
    {
        return new Call(CallType::InstanceMethod, $class, $function, $this->stackDepth, $this->caller);
    }

    /**
     * @return array<string>
     */
    private function splitUp(): array
    {
        if (str_contains($this->callTarget, '::')) {
            return explode('::', $this->callTarget);
        } elseif (str_contains($this->callTarget, '->')) {
            return explode('->', $this->callTarget);
        }

        throw new Exception("Something is off, '{$this->callTarget}' does neither contain '::' nor '->'");
    }

    private function isInstanceMethodCall(): bool
    {
        $hasArrowOperator = str_contains($this->callTarget, '->');
        return $hasArrowOperator;
    }

    private function isStaticMethodCall(): bool
    {
        $hasDoubleColonOperator = str_contains($this->callTarget, '::');
        return $hasDoubleColonOperator;
    }

    private function isStdLibCall(): bool
    {
        $isGlobal = self::isGlobalCall();
        $isWellKnown = function_exists($this->callTarget);

        return $isGlobal && $isWellKnown;
    }

    private function isGlobalCall(): bool
    {
        $isNotNative = ! self::isNativeCall();
        $hasNoClass = ! self::isStaticMethodCall() &&
                        ! self::isInstanceMethodCall();

        return $isNotNative && $hasNoClass;
    }

    private function isLanguageConstructCall(): bool
    {
        return self::isNativeCall() && str_contains($this->callTarget, ':');
    }

    private function isNativeCall(): bool
    {
        return str_contains($this->callTarget, '{') && str_ends_with($this->callTarget, '}');
    }
}
