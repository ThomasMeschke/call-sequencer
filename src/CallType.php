<?php

declare(strict_types=1);

namespace thomasmeschke\cseq;

enum CallType
{
    case Virtual;
    case Native;
    case LanguageConstruct;
    case Global;
    case StandardLibrary;
    case StaticMethod;
    case InstanceMethod;
}
