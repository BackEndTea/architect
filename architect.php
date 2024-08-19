<?php

use BackEndTea\Architect\Domain\Config\ConfigurationBuilder;
use BackEndTea\Architect\Domain\Matcher\MatcherFactory;
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
        RuleFactory::onlySelfAndNative(
            'Domain\\\\Container',
            MatcherFactory::psrMatcher(),
        )
    )
;