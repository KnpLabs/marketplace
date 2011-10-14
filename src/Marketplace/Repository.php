<?php

use Doctrine\DBAL\Connection;

abstract class Repository
{
    public $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }
}