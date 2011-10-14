<?php

namespace Repository;

use Marketplace\Repository;

class Comment extends Repository
{
    public function getTableName()
    {
        return 'comment';
    }

    public function countByProjectId($id)
    {
        return $this->db->fetchColumn('SELECT COUNT(id) FROM comment WHERE project_id = ?', array($id));
    }

    public function findByProjectId($id, $limit = 5)
    {
        $nbComments = $this->countByProjectId($id);

        $sql = 'SELECT * FROM comment WHERE project_id = ?';

        if ($limit) {
            $sql .= sprintf(' LIMIT %d, %d', $nbComments - $limit, $limit);
        }

        return $this->db->fetchAll($sql, array($id));
    }

    public function findLatests()
    {
        return $this->db->fetchAll('SELECT p.id AS project_id, p.name AS project_name, c.id, c.content_html, c.username, c.created_at FROM comment AS c JOIN project AS p on c.project_id = p.id ORDER BY c.created_at DESC, c.id DESC LIMIT 5');
    }
}