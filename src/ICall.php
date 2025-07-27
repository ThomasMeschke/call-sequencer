<?php

declare(strict_types=1);

namespace thomas\cseq;

interface ICall
{
    public function getCallType(): CallType;
    public function getCallDepth(): int;
    public function getContext(): string;
    public function getTarget(): string;
    /** @return array<Call> */
    public function getCallees(): array;
    public function addCallee(Call $callee): void;
    public function hasCallees(): bool;
    public function calleeCount(): int;
    public function lastCallee(): ?Call;
    public function getCaller(): ?Call;
    public function isFinalized(): bool;
    public function finalize(int $duration): void;
    public function selfFinalize(): void;
    public function getCost(): ?int;
    public function equals(?Call $other): bool;
}
