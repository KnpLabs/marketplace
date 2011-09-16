<?php

namespace Marketplace;

use Doctrine\DBAL\Schema\Schema;

abstract class AbstractMigration
{
    private $schema;

    public function __construct(Schema $schema)
    {
        $this->schema = $schema;
    }

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

    abstract public function up();

    abstract public function down();
}