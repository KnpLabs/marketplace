<?php

namespace Migration;

use Marketplace\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class ProjectVoteMigration extends AbstractMigration
{
    public function schemaUp(Schema $schema)
    {
        $projectVoteTable = $schema->createTable('project_vote');
        $projectVoteTable->addColumn('id', 'integer', array(
            'unsigned' => true,
            'autoincrement' => true
        ));
        $projectVoteTable->addColumn('project_id', 'integer', array('unsigned' => true));
        $projectVoteTable->addColumn('username', 'string');
        $projectVoteTable->setPrimaryKey(array('id'));
        $projectVoteTable->addUniqueIndex(array('username'));
        $projectVoteTable->addForeignKeyConstraint($schema->getTable('project'), array('project_id'), array('id'), array('onDelete' => 'CASCADE'));
    }

    public function getMigrationInfo()
    {
        return 'Added project_vote table';
    }
}