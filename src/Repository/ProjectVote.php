<?php

namespace Repository;

use Marketplace\Repository;

class ProjectVote extends Repository
{
    public function getTableName()
    {
        return 'project_vote';
    }

    public function findByProjectId($id)
    {
        return $this->db->fetchAll('SELECT username FROM project_vote WHERE project_id = ?', array($id));
    }

    public function existsForProjectAndUser($id, $username)
    {
        $sql = <<<____SQL
            SELECT p.id
            FROM project AS p
            LEFT JOIN project_vote AS v ON p.id = v.project_id
            WHERE p.id = ? AND (p.username = ? OR v.username = ?)
____SQL;

        return (bool) $this->db->fetchColumn($sql, array($id, $username, $username));
    }
}