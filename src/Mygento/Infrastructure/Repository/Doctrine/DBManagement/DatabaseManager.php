<?php

namespace App\Mygento\Infrastructure\Repository\Doctrine\DBManagement;

use Exception;

final class DatabaseManager implements DatabaseManagerInterface
{
    private string $kernelProjectDir;

    private string $environment;

    public function __construct(string $kernelProjectDir, string $environment)
    {
        $this->kernelProjectDir = $kernelProjectDir;
        $this->environment = $environment;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function dropTestDatabaseAndSchema(): void
    {
        $command = 'php ' . $this->kernelProjectDir . '/bin/console doctrine:database:drop --env=' . $this->environment . ' --force';

        $output = null;
        $exit_status = null;
        exec($command, $output, $exit_status);

        if ($exit_status !== 0) {
            throw new \Exception('An exception occurred during database dropping:' . implode('; ', $output), $exit_status);
        }
    }

    public function createTestDatabaseAndSchema(): void
    {
        $command = 'php ' . $this->kernelProjectDir . '/bin/console doctrine:database:create --env=' . $this->environment;

        $output = null;
        $exit_status = null;
        exec($command, $output, $exit_status);

        if ($exit_status !== 0) {
            throw new \Exception('An exception occurred during database creating:' . implode('; ', $output), $exit_status);
        }

        $command = 'php ' . $this->kernelProjectDir . '/bin/console doctrine:schema:create --env=' . $this->environment;

        $output = null;
        $exit_status = null;
        exec($command, $output, $exit_status);

        if ($exit_status !== 0) {
            throw new \Exception('An exception occurred during database schema creating:' . implode('; ', $output), $exit_status);
        }
    }

    public function recreateTestDatabaseAndSchema(): void
    {
        $exception = null;

        try {
            $this->dropTestDatabaseAndSchema();
        } catch (Exception $exception) {
            $exception = new \Exception('An exception occurred during database recreating:' . $exception->getMessage(), $exception->getCode());
        }

        try {
            $this->createTestDatabaseAndSchema();
            return;
        } catch (Exception $exception) {
            $exception = new \Exception('An exception occurred during database recreating:' . $exception->getMessage(), $exception->getCode(), $exception);
        }

        if ($exception !== null) {
            throw $exception;
        }
    }
}
