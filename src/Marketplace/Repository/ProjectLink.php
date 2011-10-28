<?php

namespace Marketplace\Repository;

use Marketplace\Repository;

class ProjectLink extends Repository
{
    public function getTableName()
    {
        return 'project_link';
    }

    public function findByProjectId($id)
    {
        return $this->db->fetchAll('SELECT * FROM project_link WHERE project_id = ?', array($id));
    }
}