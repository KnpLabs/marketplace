<?php

namespace Entity;

use Symfony\Component\Validator\Constraints as Assert;

class ProjectLink
{
    public $id;

    public $project_id;

    /**
     * @Assert\NotBlank()
     */
    public $label;

    /**
     * @Assert\Url()
     */
    public $url;
}