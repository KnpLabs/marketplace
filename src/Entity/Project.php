<?php

namespace Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Project
{
    public $id;

    /**
     * @Assert\NotBlank()
     */
    public $name;

    /**
     * @Assert\NotBlank()
     */
    public $description;
}