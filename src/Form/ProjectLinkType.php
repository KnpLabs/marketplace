<?php

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ProjectLinkType extends AbstractType
{
    public function getName()
    {
        return 'project_link';
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('label', 'text');
        $builder->add('url', 'text');
    }
}