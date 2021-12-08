<?php

namespace App\Tests\Mygento\Infrastructure\Repository\Doctrine;

use App\Mygento\Infrastructure\Repository\Doctrine\DBManagement\DatabaseManagerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DoctrineRepositoryTestCase extends KernelTestCase
{
    protected EntityManager|null $entityManager;

    protected DatabaseManagerInterface $databaseManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->databaseManager = $kernel->getContainer()
            ->get('mygento.doctrine.db-manager');

        $this->databaseManager->recreateTestDatabaseAndSchema();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;

        $this->databaseManager->dropTestDatabaseAndSchema();
    }
}
