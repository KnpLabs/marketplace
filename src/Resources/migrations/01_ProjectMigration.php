<?php

namespace Migration;

use Marketplace\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class ProjectMigration extends AbstractMigration
{
    public function schemaUp(Schema $schema)
    {
        $projectTable = $schema->createTable('project');
        $projectTable->addColumn('id', 'integer', array(
            'unsigned'      => true,
            'autoincrement' => true
        ));
        $projectTable->addColumn('name', 'string');
        $projectTable->addColumn('description', 'text');
        $projectTable->addColumn('username', 'string');
        $projectTable->setPrimaryKey(array('id'));
        $projectTable->addUniqueIndex(array('name'));
    }

    public function getMigrationInfo()
    {
        return 'Added project table';
    }
}