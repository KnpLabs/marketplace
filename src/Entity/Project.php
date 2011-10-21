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

    /**
     * @Assert\Choice(callback="getCategoryChoices")
     */
    public $category;

    public $description_html;

    static public function getCategoryChoices()
    {
        return array_keys($GLOBALS['app']['project.categories']);
    }
}