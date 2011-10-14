<?php

namespace Marketplace\Repository;

use Marketplace\Repository;

class Project Extends Repository
{
    public function getTableName()
    {
        return 'project';
    }

    public function findWithHasVoted($id, $username)
    {
        $sql = <<<____SQL
            SELECT
                p.*,
                (SELECT COUNT(mv.id)
                    FROM project_vote AS mv
                    WHERE project_id = p.id
                       AND mv.username = ?
                    LIMIT 1
                ) AS has_voted
            FROM project AS p
            WHERE p.id = ?
            LIMIT 1
____SQL;
        
        return $this->db->fetchAssoc($sql, array($username, $id));
    }

    public function findHomepage($username)
    {
        $sql = <<<____SQL
            SELECT
                p.id,
                p.name,
                p.description_html,
                (SELECT COUNT(v.id)
                    FROM project_vote AS v
                    WHERE project_id = p.id
                ) AS votes,
                (SELECT COUNT(mv.id)
                    FROM project_vote AS mv
                    WHERE project_id = p.id
                       AND mv.username = ?
                    LIMIT 1
                ) AS has_voted,
                (SELECT COUNT(c.id)
                    FROM comment AS c
                    WHERE c.project_id = p.id
                ) AS comments
            FROM project AS p
____SQL;

        $projects = $this->db->fetchAll($sql, array($username));

        usort($projects, function($a, $b) {
            if ($b['votes'] == $a['votes']) {
                return $b['id'] - $a['id'];
            }
            return $b['votes'] - $a['votes'];
        });

        return $projects;
    }
}