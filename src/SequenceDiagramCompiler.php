<?php

declare(strict_types=1);

namespace thomasmeschke\cseq;

class SequenceDiagramCompiler
{
    public function __construct(private CompilerOptions $options)
    {
    }

    public function compileCallGraph(Call $root, string $diagramName): string
    {
        $result = "@startuml {$diagramName}" . PHP_EOL;

        $result .= $this->compile($root);

        $result .= "@enduml" . PHP_EOL;

        return $result;
    }

    private function compile(Call $call): string
    {
        $call = $this->preprocessCall($call);

        $from = $call->getCaller()?->getContext() ?? '[';
        $to = sprintf("%s ++:%s", $call->getContext(), $call->getTarget());

        $result = sprintf("%s->%s", $from, $to) . PHP_EOL;

        $endRecursion = false;
        $cutOffNamespaces = $this->options->cutOffNamespaces;
        foreach($cutOffNamespaces as $namespace) {
            $namespace = str_replace('\\', '_', $namespace);
            if (str_starts_with($call->getContext(), $namespace)) {
                $endRecursion = true;
                break;
            }
        }

        if (! $endRecursion) {
            $callees = $call->getCallees();
            foreach ($callees as $callee) {
                $result .= $this->compile($callee);
            }
        }

        $result .= 'return' . PHP_EOL;

        return $result;
    }

    private function preprocessCall(Call $call): Call
    {
        [$context, $target] = [$call->getContext(), $call->getTarget()];

        $context = trim($context, '{}');
        $target = trim($target, '{}');

        if ($call->getCallType() === CallType::LanguageConstruct) {
            $firstColonPosition = (int)strpos($target, ':');
            $construct = substr($target, 0, $firstColonPosition);
            $file = substr($target, $firstColonPosition + 1);
            $file = str_replace($this->options->basePath, '', $file);
            $target = sprintf("%s(%s)", $construct, $file);
        }

        $context = str_replace('\\', '_', $context);
        $target = str_replace('\\', '_', $target);

        foreach ($this->options->replaceOptions as $search => $replace) {
            $context = str_replace($search, $replace, $context);
            $target = str_replace($search, $replace, $target);
        }

        $call->setContext($context);
        $call->setTarget($target);

        return $call;
    }
}
