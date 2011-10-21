<?php

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use Symfony\Component\Validator\Constraints as Assert;

class ProjectType extends AbstractType
{
    public function getName()
    {
        return 'project';
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name', 'text');
        $builder->add('description', 'textarea');
        $builder->add('category', 'choice', array('choices' => $options['categories']));
    }

    public function getDefaultOptions(array $options)
    {
        $options = array_merge(array(
            'categories' => array('none' => 'No category'),
        ), $options);

        $options['validation_constraint'] = new Assert\Collection(array(
            'fields' => array(
                'name'        => new Assert\NotBlank(),
                'description' => new Assert\NotBlank(),
                'category'    => new Assert\Choice(array(
                    'choices' => array_keys($options['categories'])
                ))
            ),
            'allowExtraFields' => true,
        ));

        return $options;
    }
}