<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Infrastructure\Command;

use BackEndTea\Architect\Domain\Config\Configuration;
use BackEndTea\Architect\Domain\Config\ConfigurationBuilder;
use BackEndTea\Architect\Domain\Container\ArchitecContainer;
use BackEndTea\Architect\Infrastructure\Runner;
use InvalidArgumentException;
use Roave\BetterReflection\BetterReflection;
use SplFileInfo;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

use function array_merge;
use function count;
use function get_debug_type;
use function getcwd;
use function is_string;
use function realpath;
use function sprintf;

#[AsCommand(
    name: 'run',
    description: 'Run architect',
)]
class RunCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addOption('config', 'c', InputOption::VALUE_REQUIRED, 'Path to the configuration file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configuration = $this->buildConfiguration($input);

        $io = new SymfonyStyle($input, $output);

        $io->writeln(
            sprintf(
                'Note: Using configuration file %s',
                $configuration->configLocation,
            ),
        );
        $reflector =  (new BetterReflection())
            ->reflector();

        $runner = new Runner($reflector);
        $errors = [];
        $hasRan = false;

        $container = new ArchitecContainer();
        $container->set(SymfonyStyle::class, $io);
        $container->set(Filesystem::class, new Filesystem());

        $io->writeln('Running Architect');

        $paths    = self::iterableToArray($configuration->paths);
        $progress = new ProgressBar($output, count($paths));

        foreach ($paths as $path) {
            $progress->advance();
            $hasRan = true;
            $errors = array_merge($errors, $runner->run($configuration, $path));
        }

        $progress->finish();
        $io->writeln('');
        $io->writeln('');
        $io->writeln('');

        if (! $hasRan) {
            $output->writeln('No paths configured to run');

            return Command::FAILURE;
        }

        if ($errors !== []) {
            foreach ($configuration->printers as $printer) {
                $printer->setUp($container);
                $printer->print($errors);
            }

            return Command::FAILURE;
        }

        $io->success('No errors found');

        return Command::SUCCESS;
    }

    private function buildConfiguration(InputInterface $input): Configuration
    {
        $path = $input->getOption('config');

        if ($path === null) {
            $path = getcwd() . '/architect.php';
            if(! file_exists($path)) {
                throw new InvalidArgumentException(
                    'Expecting a file called architect.php to start.'
                );
            }
        }

        if (! is_string($path)) {
            throw new InvalidArgumentException('Configuration path should be a string');
        }

        $path = realpath($path);
        if (! $path) {
            throw new InvalidArgumentException('Configuration file should be a valid file');
        }

        $config = require $path;

        if ($config instanceof ConfigurationBuilder) {
            return $config->build()->withConfigLocation($path);
        }

        if ($config instanceof Configuration) {
            return $config->withConfigLocation($path);
        }

        throw new InvalidArgumentException(
            sprintf(
                'Configuration file should return a Configuration(Builder) object instead got: "%s"',
                get_debug_type($config),
            ),
        );
    }

    /**
     * @param iterable<SplFileInfo> $it
     *
     * @return array<SplFileInfo>
     */
    private static function iterableToArray(iterable $it): array
    {
        $result = [];
        foreach ($it as $item) {
            $result[] = $item;
        }

        return $result;
    }
}
