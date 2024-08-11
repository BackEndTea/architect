<?php

use BackEndTea\Architect\Domain\Config\ConfigurationBuilder;
use BackEndTea\Architect\Domain\Matcher\GlobFile;
use BackEndTea\Architect\Domain\Matcher\NamespaceRegex;
use BackEndTea\Architect\Domain\Rule\Rule;
use Symfony\Component\Finder\Finder;

return ConfigurationBuilder::create()
    ->paths(Finder::create()
        ->in('src/Domain')
        ->in('tests')
        ->name('*.php')
        ->files())
    ->addRule(
        new Rule(
            from: new NamespaceRegex(
                '#BackEndTea\\\Architect\\\Domain#'
            ),
            to: new \BackEndTea\Architect\Domain\Matcher\Any(
                new NamespaceRegex(
                '/BackEndTea\\\\Architect\\\\Infrastructure/'
                ),
                new NamespaceRegex(
                    '/BackEndTea\\\\Architect\\\\Application/'
                )
            )
        ),
        new Rule(
            from: new GlobFile(__DIR__ .'/src/**/*'),
            to: new GlobFile(__DIR__ . '/tests/**/*')
        )
    )
    ->build();