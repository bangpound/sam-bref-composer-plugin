<?php

namespace Bangpound\Bref\Bridge\Command;

use Aws\Sdk;
use Composer\InstalledVersions;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Composer\Command\BaseCommand;

class BrefLayersArnCommand extends BaseCommand
{
    public function __construct(private readonly Sdk $sdk)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('bref:layers:arn')
            ->addOption('architecture', 'a', InputOption::VALUE_REQUIRED)
            ->addOption('region', 'r', InputOption::VALUE_REQUIRED)
            ->addOption('fpm', null, InputOption::VALUE_NONE)
            ->addOption('console', null, InputOption::VALUE_NONE)
            ->addArgument('layer', null, InputArgument::REQUIRED)
            ->addArgument('version', null, InputArgument::REQUIRED)
            ->addOption('all')
        ;
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        $path = InstalledVersions::getInstallPath('bref/bref');
        $layers = json_decode(file_get_contents($path . '/layers.json'), true);

        $layer = $input->getArgument('layer');
        if ($input->getOption('console') && !$input->getArgument('layer')) {
            $layer = 'console';
            $input->setArgument('layer', $layer);
        }

        if (!$input->getArgument('layer')) {
            $versions = array_filter(array_values(array_unique(array_filter(array_map(function ($layer) {
                return preg_replace('#^(?:arm-)?php-(\d+)(?:-fpm)?$#', '$1', $layer);
            }, array_keys($layers))))), function ($version) {
                return $version !== 'console';
            });

            $architectures = ['arm64', 'x86_64'];
            if (!$input->getOption('architecture')) {
                $input->setOption('architecture', $io->choice('Select the arch', $architectures, 'x86_64'));
            }
            $layer = $input->getOption('architecture') === 'arm64' ? 'arm-' : '';
            $layer .= 'php-';
            $layer .= $io->choice('Select the PHP version', $versions, default: '83');
            $layer .= $input->getOption('fpm') ? '-fpm' : '';
            $input->setArgument('layer', $layer);
        }

        if (!$input->getOption('region')) {
            $regions = array_unique(array_merge(...array_map(function ($layer) {
                return array_keys($layer);
            }, array_values($layers))));

            $defaultRegion = $this->sdk->createSts()->getRegion();

            $input->setOption('region', $io->choice('Select the region', $regions, default: $defaultRegion));
        }

        if (!$input->getArgument('version')) {
            $input->setArgument('version', $layers[$layer][$input->getOption('region')]);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->writeln(implode(':', [
            'arn' => 'arn',
            'partition' => 'aws',
            'service' => 'lambda',
            'region' => $input->getOption('region'),
            'account_id' => '534081306603',
            'resource' => 'layer:' . $input->getArgument('layer') . ':' . $input->getArgument('version'),
        ]));

        return Command::SUCCESS;
    }
}
