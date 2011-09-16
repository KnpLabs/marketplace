<?php

namespace Migration;

use Marketplace\AbstractMigration;

class CommentMigration extends AbstractMigration
{
    public function up()
    {
        $commentTable = $this->getSchema()->createTable('comment');
        $commentTable->addColumn('id', 'integer', array(
            'unsigned'      => true,
            'autoincrement' => true
        ));
        $commentTable->addColumn('content', 'text');
        $commentTable->addColumn('project_id', 'integer', array('unsigned' => true));
        $commentTable->addColumn('username', 'string');
        $commentTable->setPrimaryKey(array('id'));
        $commentTable->addForeignKeyConstraint($this->getSchema()->getTable('project'), array('project_id'), array('id'), array('onDelete' => 'CASCADE'));
    }

    public function down()
    {
        throw new \RuntimeException('Unsupported operation');       
    }
}