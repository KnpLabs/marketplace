<?php

namespace Marketplace;

use Doctrine\DBAL\Schema\Schema;
use Silex\Application;

abstract class AbstractMigration
{
    public function getVersion()
    {
        $rc = new \ReflectionClass($this);

        if (preg_match('/^(\d+)/', basename($rc->getFileName()), $matches)) {
            return (int) ltrim($matches[1], 0);
        }

        throw new RuntimeError(sprintf('Could not get version from "%"', basename($rc->getFileName())));
    }

    public function getSchema()
    {
        return $this->schema;
    }

    public function getMigrationInfo()
    {
        return null;
    }

    public function schemaUp(Schema $schema) {}

    public function schemaDown(Schema $schema) {}

    public function appUp(Application $app) {}

    public function appDown(Application $app) {}
}