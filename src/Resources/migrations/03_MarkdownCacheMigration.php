<?php

namespace Migration;

use Marketplace\AbstractMigration;

class MarkdownCacheMigration extends AbstractMigration
{
    public function up()
    {
        $project = $this->getSchema()->getTable('project');
        $project->addColumn('description_html', 'text');

        $comment = $this->getSchema()->getTable('comment');
        $comment->addColumn('content_html', 'text');
    }

    public function down() {}
}