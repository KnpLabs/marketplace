<?php

namespace Migration;

use Marketplace\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class ProjectsLastCommentedAtNullMigration extends AbstractMigration
{
    public function schemaUp(Schema $schema)
    {
        $project = $schema->getTable('project');
        $project->changeColumn('last_commented_at', array('default' => null, 'notnull' => false));
    }

    public function getMigrationInfo()
    {
        return 'Changed project.last_commented_at to allow null values';
    }
}