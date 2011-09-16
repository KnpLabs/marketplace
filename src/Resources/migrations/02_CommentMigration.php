<?php

namespace Migration;

use Marketplace\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class CommentMigration extends AbstractMigration
{
    public function schemaUp(Schema $schema)
    {
        $commentTable = $schema->createTable('comment');
        $commentTable->addColumn('id', 'integer', array(
            'unsigned'      => true,
            'autoincrement' => true
        ));
        $commentTable->addColumn('content', 'text');
        $commentTable->addColumn('project_id', 'integer', array('unsigned' => true));
        $commentTable->addColumn('username', 'string');
        $commentTable->setPrimaryKey(array('id'));
        $commentTable->addForeignKeyConstraint($schema->getTable('project'), array('project_id'), array('id'), array('onDelete' => 'CASCADE'));
    }

    public function getMigrationInfo()
    {
        return 'Added comment table';
    }
}