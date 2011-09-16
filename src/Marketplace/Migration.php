<?php

namespace Marketplace;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\Finder\Finder;

class Migration
{
    private $schema;

    private $conn;

    private $current_version = null;

    public function __construct(Schema $schema, Connection $conn, Finder $finder = null)
    {
        $this->schema   = $schema;
        $this->toSchema = clone($schema);
        $this->conn     = $conn;
        $this->finder   = $finder ?: new Finder();
    }

    private function buildSchema(Schema $schema)
    {
        $queries = $this->schema->getMigrateToSql($schema, $this->conn->getDatabasePlatform());

        foreach ($queries as $query) {
            $this->conn->exec($query);
        }
    }

    private function findMigrations($from)
    {
        $finder     = clone($this->finder);
        $migrations = array();

        foreach ($finder->files()->name('*Migration.php')->in(__DIR__.'/../Resources/migrations') as $migration) {
            if (preg_match('/^(\d+)_(.*Migration).php$/', basename($migration), $matches)) {

                list(, $version, $class) = $matches;

                if ((int) ltrim($version, 0) > $from) {
                    require_once $migration;

                    $fqcn = '\\Migration\\'.$class;

                    if (!class_exists($fqcn)) {
                        throw new \RuntimeException(sprintf('Could not find class "%s" in "%s"', $fqcn, $migration));
                    }

                    $migrations[] = new $fqcn($this->toSchema);
                }
            }
        }

        return $migrations;
    }

    public function getCurrentVersion()
    {
        if (is_null($this->current_version)) {
            $this->current_version = $this->conn->fetchColumn('SELECT schema_version FROM schema_version');
        }

        return $this->current_version;
    }

    public function setCurrentVersion($version)
    {
        $this->current_version = $version;
        $this->conn->update('schema_version', array('schema_version' => $version), array(1 => 1));
    }

    public function hasVersionInfo()
    {
        return $this->schema->hasTable('schema_version');
    }

    public function createVersionInfo()
    {
        $schema = clone($this->schema);

        $schemaVersion = $schema->createTable('schema_version');
        $schemaVersion->addColumn('schema_version', 'integer', array('unsigned' => true, 'default' => 0));

        $this->buildSchema($schema);

        $this->conn->insert('schema_version', array('schema_version' => 0));
    }

    public function migrate()
    {
        $from    = $this->conn->fetchColumn('SELECT schema_version FROM schema_version');
        $queries = array();

        $migrations = $this->findMigrations($from);

        if (count($migrations) == 0) {
            return true;
        }

        foreach ($migrations as $migration) {
            $migration->up();
        }

        $this->buildSchema($this->toSchema);

        $this->setCurrentVersion($migration->getVersion());
    }
}