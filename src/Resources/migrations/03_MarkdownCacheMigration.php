<?php

namespace Migration;

use Marketplace\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Silex\Application;

class MarkdownCacheMigration extends AbstractMigration
{
    public function schemaUp(Schema $schema)
    {
        $project = $schema->getTable('project');
        $project->addColumn('description_html', 'text');

        $comment = $schema->getTable('comment');
        $comment->addColumn('content_html', 'text');
    }

    public function appUp(Application $app)
    {
        foreach ($app['db']->fetchAll('SELECT id, description FROM project') as $project) {
            $project['description_html'] = $app['markdown']($project['description']);
            $app['db']->update('project', $project, array('id' => $project['id']));
        }
    }

    public function getMigrationInfo()
    {
        return 'Implemented database markdown cache';
    }
}