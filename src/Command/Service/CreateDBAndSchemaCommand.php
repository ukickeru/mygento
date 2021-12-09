<?php

namespace App\Command\Service;

use App\Mygento\Infrastructure\Repository\Doctrine\DBManagement\DatabaseManagerInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'service:db:create',
    description: 'Create database for current environment',
)]
class CreateDBAndSchemaCommand extends Command
{
    protected DatabaseManagerInterface $databaseManager;

    public function __construct(string $name = null, DatabaseManagerInterface $databaseManager)
    {
        parent::__construct($name);

        $this->databaseManager = $databaseManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $confirm = $io->confirm('Create database for "' . $this->databaseManager->getEnvironment() . '" environment?', false);

        if ($confirm) {
            try {
                $this->databaseManager->createTestDatabaseAndSchema();
            } catch (Exception $exception) {
                $io->note('An exception occurred during command execution: ' . $exception->getMessage());
                return Command::FAILURE;
            }
        } else {
            $io->note('Command execution was cancelled!');
            return Command::FAILURE;
        }

        $io->success('Database for "' . $this->databaseManager->getEnvironment() . '" environment created successfully!');

        return Command::SUCCESS;
    }
}
