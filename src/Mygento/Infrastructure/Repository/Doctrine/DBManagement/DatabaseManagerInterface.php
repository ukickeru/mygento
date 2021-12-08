<?php

namespace App\Mygento\Infrastructure\Repository\Doctrine\DBManagement;

use Exception;

interface DatabaseManagerInterface
{
    public function getEnvironment(): string;

    /**
     * @throws Exception
     */
    public function createTestDatabaseAndSchema(): void;

    /**
     * @throws Exception
     */
    public function dropTestDatabaseAndSchema(): void;

    /**
     * @throws Exception
     */
    public function recreateTestDatabaseAndSchema(): void;
}
