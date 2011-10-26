<?php

namespace Marketplace;

use Silex\Application;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\Finder\Finder;

class Migration
{
    private $app;

    private $schema;

    private $conn;

    private $current_version = null;

    private $migration_infos = array();

    public function __construct(Application $app, Schema $schema, Finder $finder = null)
    {
        $this->schema   = $schema;
        $this->toSchema = clone($schema);
        $this->app      = $app;
        $this->conn     = $app['db'];
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

        $finder
            ->files()
            ->name('*Migration.php')
            ->in(__DIR__.'/../Resources/migrations')
            ->sortByName()
        ;

        foreach ($finder as $migration) {
            if (preg_match('/^(\d+)_(.*Migration).php$/', basename($migration), $matches)) {

                list(, $version, $class) = $matches;

                if ((int) ltrim($version, 0) > $from) {
                    require_once $migration;

                    $fqcn = '\\Migration\\'.$class;

                    if (!class_exists($fqcn)) {
                        throw new \RuntimeException(sprintf('Could not find class "%s" in "%s"', $fqcn, $migration));
                    }

                    $migrations[] = new $fqcn();
                }
            }
        }

        return $migrations;
    }

    public function getMigrationInfos()
    {
        return $this->migrationInfos;
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
        $this->conn->executeUpdate('UPDATE schema_version SET schema_version = ?', array($version));
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
            return null;
        }

        foreach ($migrations as $migration) {
            $migration->schemaUp($this->toSchema);
        }

        $this->buildSchema($this->toSchema);

        foreach ($migrations as $migration) {
            $migration->appUp($this->app);
        }

        $migrationInfos = array();

        foreach ($migrations as $migration) {
            if (null !== $migration->getMigrationInfo()) {
                $migrationInfos[$migration->getVersion()] = $migration->getMigrationInfo();
            }
        }

        $this->migrationInfos = $migrationInfos;

        $this->setCurrentVersion($migration->getVersion());

        return true;
    }
}
