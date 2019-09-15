<?php

namespace App\Command;

use App\Cache\CacheLibrary;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CacheCommand extends Command
{
    protected static $defaultName = 'cache';

    /**
     * @var CacheLibrary
     */
    private $cache;

    public function __construct(CacheLibrary $cacheLibrary)
    {
        parent::__construct();

        $this->cache = $cacheLibrary;
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('key', InputArgument::REQUIRED, 'Cache key')
            ->addArgument('value', InputArgument::OPTIONAL, 'Cache value')
            ->addOption('del', 'd', InputOption::VALUE_NONE, 'Remove key from cache');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io    = new SymfonyStyle($input, $output);
        $key   = $input->getArgument('key');
        $value = $input->getArgument('value');
        $del = $input->getOption('del');

        $result = $this->cache->get($key);

        if ($del) {
            $this->cache->delete($key);

            $io->success(sprintf('Key %s removed from redis', $key));

            return;
        }

        if ($value) {
            $this->cache->set($key, $value);

            $io->success(sprintf('Cache key %s set to %s', $key, $value));

            return;
        }

        $io->success('Resultado: ' . $result);
    }
}
