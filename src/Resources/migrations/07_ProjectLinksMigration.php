<?php

namespace Migration;

use Marketplace\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class ProjectLinksMigration extends AbstractMigration
{
    public function schemaUp(Schema $schema)
    {
        $projectLink = $schema->createTable('project_link');
        $projectLink->addColumn('id', 'integer', array(
            'unsigned'      => true,
            'autoincrement' => true
        ));

        $projectLink->addColumn('project_id', 'integer', array('unsigned' => true));
        $projectLink->addColumn('label', 'string');
        $projectLink->addColumn('url', 'string');
        $projectLink->setPrimaryKey(array('id'));
        $projectLink->addForeignKeyConstraint($schema->getTable('project'), array('project_id'), array('id'), array('onDelete' => 'CASCADE'));
    }

    public function getMigrationInfo()
    {
        return 'Added project_link table';
    }
}