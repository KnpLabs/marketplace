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

    public function insert(array $data)
    {
        return $this->db->insert($this->getTableName(), $data);
    }

    public function update(array $data, array $identifier)
    {
        return $this->db->update($this->getTableName(), $data, $identifier);
    }

    public function delete(array $identifier)
    {
        return $this->db->delete($this->getTableName(), $identifier);
    }

    public function find($id)
    {
        return $this->db->fetchAssoc(sprintf('SELECT * FROM %s WHERE id = ? LIMIT 1', $this->getTableName()), array($id));
    }
}