<?php

declare(strict_types=1);

namespace thomasmeschke\cseq;

class CallPrinter
{
    private static int $indentation = 0;

    public static function printCall(Call $call): void
    {
        echo str_pad('', self::$indentation, ' ', STR_PAD_LEFT);
        if ($call->getCallType() === CallType::Virtual) {
            echo self::buildVirtualCallString($call);
        } elseif ($call->getCallType() === CallType::Native) {
            echo self::buildNativeCallString($call);
        } elseif ($call->getCallType() === CallType::LanguageConstruct) {
            echo self::buildLanguageConstructCallString($call);
        } elseif ($call->getCallType() === CallType::Global) {
            echo self::buildGlobalCallString($call);
        } elseif ($call->getCallType() === CallType::StandardLibrary) {
            echo self::buildStdLibCallString($call);
        } elseif ($call->getCallType() === CallType::StaticMethod) {
            echo self::buildStaticCallString($call);
        } elseif ($call->getCallType() === CallType::InstanceMethod) {
            echo self::buildInstanceCallString($call);
        }

        echo sprintf(" (%s)", $call->getCost());

        echo PHP_EOL;

        self::$indentation += 2;
        $callees = $call->getCallees();
        foreach ($callees as $callee) {
            self::printCall($callee);
        }
        self::$indentation -= 2;
    }

    private static function buildVirtualCallString(Call $call): string
    {
        return sprintf("%s", $call->getContext());
    }

    private static function buildNativeCallString(Call $call): string
    {
        return sprintf("%s", $call->getTarget());
    }

    private static function buildLanguageConstructCallString(Call $call): string
    {
        return sprintf("%s", $call->getTarget());
    }

    private static function buildGlobalCallString(Call $call): string
    {
        return sprintf("%s", $call->getTarget());
    }

    private static function buildStdLibCallString(Call $call): string
    {
        return sprintf("%s", $call->getTarget());
    }

    private static function buildStaticCallString(Call $call): string
    {
        return sprintf("%s::%s", $call->getContext(), $call->getTarget());
    }

    private static function buildInstanceCallString(Call $call): string
    {
        return sprintf("%s->%s", $call->getContext(), $call->getTarget());
    }
}
