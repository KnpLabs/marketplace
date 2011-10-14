<?php

namespace Marketplace;

use Doctrine\DBAL\Connection;

abstract class Repository
{
    abstract public function getTableName();

    public $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function find($id)
    {
        return $this->db->fetchAssoc(sprintf('SELECT * FROM %s WHERE id = ? LIMIT 1', $this->getTableName()), array($id));
    }
}