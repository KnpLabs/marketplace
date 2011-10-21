<?php

namespace Migration;

use Marketplace\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class ProjectsCategoryMigration extends AbstractMigration
{
    public function schemaUp(Schema $schema)
    {
        $project = $schema->getTable('project');
        $project->addColumn('category', 'string', array('default' => 'none'));
    }

    public function getMigrationInfo()
    {
        return 'Added project.category field';
    }
}