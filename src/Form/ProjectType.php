<?php

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ProjectType extends AbstractType
{
    public function getName()
    {
        return 'project';
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('id', 'hidden');
        $builder->add('name', 'text');
        $builder->add('description', 'textarea');
        $builder->add('category', 'choice', array('choices' => $options['categories']));
    }

    public function getDefaultOptions(array $options)
    {
        return array_merge($options, array(
            'categories' => array('none' => 'No category'),
        ));
    }
}