<?php

namespace Repository;

use Marketplace\Repository;

class Project Extends Repository
{
    public function getTableName()
    {
        return 'project';
    }

    public function getListQuery()
    {
        return
            'SELECT
                p.*,
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
            FROM project AS p';
    }

    public function findByCategory($category, $username)
    {
        $sql = $this->getListQuery().' WHERE p.category = ? ORDER BY votes DESC, comments DESC, p.created_at DESC, p.id DESC';

        return $this->db->fetchAll($sql, array($username, $category));
    }

    public function findLatestsByCategory($category, $username)
    {
        $sql = $this->getListQuery().' WHERE p.category = ? ORDER BY p.created_at DESC, p.id DESC LIMIT 5';

        return $this->db->fetchAll($sql, array($username, $category));
    }

    public function findLatests($username)
    {
        return $this->db->fetchAll($this->getListQuery().' ORDER BY p.created_at DESC, p.id DESC LIMIT 5', array($username));
    }

    public function findWithHasVoted($id, $username)
    {
        $sql = 
            'SELECT
                p.*,
                (SELECT COUNT(mv.id)
                    FROM project_vote AS mv
                    WHERE project_id = p.id
                       AND mv.username = ?
                    LIMIT 1
                ) AS has_voted
            FROM project AS p
            WHERE p.id = ?
            LIMIT 1';
        
        return $this->db->fetchAssoc($sql, array($username, $id));
    }

    public function findHomepage($username)
    {
        $projects = $this->db->fetchAll($this->getListQuery().' ORDER BY votes DESC, comments DESC, p.created_at DESC, p.id DESC', array($username));

        return $projects;
    }
}