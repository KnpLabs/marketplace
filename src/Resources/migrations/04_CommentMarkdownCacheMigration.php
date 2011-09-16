<?php

namespace Migration;

use Marketplace\AbstractMigration;
use Silex\Application;

class CommentMarkdownCacheMigration extends AbstractMigration
{
    public function appUp(Application $app)
    {
        foreach ($app['db']->fetchAll('SELECT id, content FROM comment') as $comment) {
            $comment['content_html'] = $app['markdown']($comment['content']);
            $app['db']->update('comment', $comment, array('id' => $comment['id']));
        }
    }

    public function getMigrationInfo()
    {
        return 'Comment markdown cache';
    }
}