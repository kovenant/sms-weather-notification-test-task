<?php

declare(strict_types=1);

namespace App\Service\Output;

use App\Contract\OutputServiceInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Output service based on symfony console package
 */
final class SymfonyOutputService implements OutputServiceInterface
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @param string $string
     */
    public function print(string $string): void
    {
        $this->output->setDecorated(false);
        $this->output->writeln('<info>' . $string . '</info>');
    }

    /**
     * @param string $string
     */
    public function printError(string $string): void
    {
        $this->output->setDecorated(true);
        $this->output->writeln('<error>' . $string . '</error>');
    }

    /**
     * @param string $string
     */
    public function printSuccess(string $string): void
    {
        $this->output->setDecorated(true);
        $this->output->writeln('<info>' . $string . '</info>');
    }
}
