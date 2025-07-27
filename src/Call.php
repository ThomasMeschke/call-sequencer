<?php

declare(strict_types=1);

namespace thomas\cseq;

class Call implements ICall
{
    /**
     * @var array<Call> $callees
     */
    private array $callees = [];
    private ?int $cost = null;

    public function __construct(
        private CallType $type,
        private string $context,
        private string $target,
        private int $stackDepth = 0,
        private ?Call $caller = null
    ) {

    }

    public function getCallType(): CallType
    {
        return $this->type;
    }

    public function getCallDepth(): int
    {
        return $this->getCallDepth();
    }

    public function getContext(): string
    {
        return $this->context;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    /** @return array<Call> */
    public function getCallees(): array
    {
        return $this->callees;
    }

    public function addCallee(Call $callee): void
    {
        $this->callees[] = $callee;
    }

    public function hasCallees(): bool
    {
        return !empty($this->callees);
    }

    public function calleeCount(): int
    {
        return count($this->callees);
    }

    public function lastCallee(): ?Call
    {
        $lastCallee = end($this->callees) ?: null;
        reset($this->callees);

        return $lastCallee;
    }

    public function getCaller(): ?Call
    {
        return $this->caller;
    }

    public function isFinalized(): bool
    {
        return null !== $this->cost;
    }

    public function finalize(int $cost): void
    {
        $this->cost = $cost;
    }

    public function selfFinalize(): void
    {
        $calculatedCost = array_reduce($this->callees, function ($carry, $callee) {
            return $carry + $callee->getCost();
        }, 0);

        $this->finalize($calculatedCost);
    }

    public function getCost(): ?int
    {
        return $this->cost;
    }

    public function setContext(string $context): void
    {
        $this->context = $context;
    }

    public function setTarget(string $target): void
    {
        $this->target = $target;
    }

    public function equals(?Call $other): bool
    {
        if (null === $other) {
            return false;
        }

        return (
            $this->context === $other->context &&
            $this->target === $other->target &&
            $this->stackDepth === $other->stackDepth
        );
    }
}
