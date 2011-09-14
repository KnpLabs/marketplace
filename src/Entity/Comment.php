<?php

namespace Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Comment
{
    public $id;

    public $project_id;

    /**
     * @Assert\NotBlank()
     */
    public $content;
}