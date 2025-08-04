[![Packagist Version](https://img.shields.io/packagist/v/thomasmeschke/call-sequencer)](https://packagist.org/packages/thomasmeschke/call-sequencer)
[![GitHub branch check runs](https://img.shields.io/github/check-runs/thomasmeschke/call-sequencer/main?label=checks%20on%20main)](https://github.com/ThomasMeschke/call-sequencer/actions/workflows/php.yml)


# Call-Sequencer
Simple tool that allows to convert xdebug trace files into plantUML sequence diagrams.

## Installation

You can include this tool into your project using composer:
```
composer require --dev thomasmeschke/call-sequencer
```

## Usage:
```
php cseq.php -i<inputfile> -o<outputfile> [-c<optionsfile>]
```

The options are as follows:
```text
-i<inputfile>    required  The xdebug trace file to process
-o<outputfile>   required  The plantUML output file to generate
-c<optionsfile>  optional  A JSON file containing the compiler options
```

## Inputfiles
The tool takes xdebug trace files (*.xt) with the trace-format configured to "3".


## Outputfiles
The tool prints files in the PlantUML format. The content can be pasted into [the online PlantUML Editor][1] or can be viewed and exportet with tools like the [VSCode PlantUML Extension][2].


## Compiler option files
The compiler options JSON file is structured as follows:
```json
{
    "basePath": "",
    "cutOffNamespaces": [],
    "replaceOptions": {
    }
}
```
`basePath(string)`: Path segment to be removed from absolute paths, e.g. the projects root path. This gets removed from all absolute paths, like include paths.

`cutOffNamespace(array)`: List of namespaces that the diagram will not follow into, e.g. the used frameworks base namespace. Only the first stackframe inside these namespaces will be included in every callstack.

`replaceOptions(object)`: A collection of keys and string values, specifying which string is to be replace with which. Can be used to redact sensitive information like company names from the resulting diagram.


## Planned features
The future might bring additional features like the possibility to choose the input trace file format via CLI parameter, as well as other output formats like flame charts.

---

[1]: https://plantuml.com/ "PlantUML web page featuring an online server editor and renderer"
[2]: https://marketplace.visualstudio.com/items?itemName=jebbs.plantuml "VSCode Extension Marketplace page for the PlantUML Extension"