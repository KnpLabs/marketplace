<?php

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use Symfony\Component\Validator\Constraints as Assert;

class ProjectLinkType extends AbstractType
{
    public function getName()
    {
        return 'project_link';
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('label', 'text');
        $builder->add('url', 'url');
    }

    public function getDefaultOptions(array $options)
    {
        $options = array_merge(array(
            'validation_constraint' => new Assert\Collection(array(
                'fields' => array(
                    'label' => new Assert\NotBlank(),
                    'url'   => new Assert\Url(),
                ),
                'allowExtraFields' => true,
            ))
        ));

        return $options;
    }
}