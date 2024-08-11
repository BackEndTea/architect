<?php

return \BackEndTea\Architect\Domain\Config\ConfigurationBuilder::create()
    ->paths(\Symfony\Component\Finder\Finder::create()
        ->in('src')
        ->in('tests')
        ->name('*.php')
        ->files())
    ->addRule(
        new \BackEndTea\Architect\Domain\Rule\Rule(
            new \BackEndTea\Architect\Domain\Matcher\NamespaceRegex(
                '#BackEndTea\\\Architect\\\Domain#'
            ),
            new \BackEndTea\Architect\Domain\Matcher\NamespaceRegex(
                '/BackEndTea\\\\Architect\\\\Infrastructure/'
            )
        )
    )
    ->build();