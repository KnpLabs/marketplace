<?php

namespace Migration;

use Marketplace\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Silex\Application;

class ProjectCommentTimestampsMigration extends AbstractMigration
{
    public function schemaUp(Schema $schema)
    {
        $project = $schema->getTable('project');
        $project->addColumn('created_at', 'datetime', array('default' => 'CURRENT_TIMESTAMP'))->setPlatformOption('version', true);
        $project->addColumn('last_commented_at', 'datetime', array('default' => null))->setPlatformOption('version', true);

        $comment = $schema->getTable('comment');
        $comment->addColumn('created_at', 'datetime', array('default' => 'CURRENT_TIMESTAMP'))->setPlatformOption('version', true);

        $projectVote = $schema->getTable('project_vote');
        $projectVote->addColumn('created_at', 'datetime', array('default' => 'CURRENT_TIMESTAMP'))->setPlatformOption('version', true);
    }

    public function appUp(Application $app)
    {
        $app['db']->exec('UPDATE project SET created_at = NOW()');
        $app['db']->exec('UPDATE comment SET created_at = NOW()');
        $app['db']->exec('UPDATE project_vote SET created_at = NOW()');

        // I guess there's a way to do that in a single query, but I'm too limited in SQL for that :/
        foreach ($app['db']->fetchAll('SELECT project_id, MAX(created_at) AS last_commented_at FROM comment GROUP BY project_id') as $comment) {
            $app['db']->update('project', array('last_commented_at' => $comment['last_commented_at']), array('id' => $comment['project_id']));
        }
    }

    public function getMigrationInfo()
    {
        return 'Added timestamps to projects and comments';
    }
}