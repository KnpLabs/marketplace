<?php

namespace Migration;

use Marketplace\AbstractMigration;

class ProjectMigration extends AbstractMigration
{
    public function up()
    {
        $projectTable = $this->getSchema()->createTable('project');
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

    public function down()
    {
        throw new \RuntimeException('Unsupported operation');
    }
}