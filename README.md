# Architect

Architect helps enforce architectural decisions in your PHP projects. 

For example, you can enforce that the `src/` folder does not use the `test/` folder,
or you can enforce a layered architecture, where you split up your code in a `Domain`, `Application`, 
and `Infrastructure` layer, where layers can only have dependencies inward.

Architect is currently very much in beta, so any provided feedback is really helpful.

## Installation

```bash
composer require backendtea/architect --dev
```

## Usage

### Config

Architect uses a php config file, by default it uses `architect.php` in the current directory.
You can pass another file with the `-c` flag if so desired.

A basic configuration file, which checks the `src` and `tests` folder files,
and has the `layered architecture` and `no src to test` rulesets.

```php
<?php

use BackEndTea\Architect\Domain\Config\ConfigurationBuilder;
use BackEndTea\Architect\Domain\Rule\RuleFactory;
use Symfony\Component\Finder\Finder;

return ConfigurationBuilder::create()
    ->paths(Finder::create()
        ->in('src')
        ->in('tests')
        ->name('*.php')
        ->files())
    ->addRule(
        RuleFactory::layeredArchitecture(),
        RuleFactory::noSrcToTest(),
    )
;
```

## Running Architect

When you have a configuration, you can run architect with:

```bash
vendor/bin/architect
```