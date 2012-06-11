<?php

namespace Marketplace\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectLinkType extends AbstractType
{
    public function getName()
    {
        return 'project_link';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('label', 'text');
        $builder->add('url', 'url');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'validation_constraint' => new Assert\Collection(array(
                'fields' => array(
                    'label' => new Assert\NotBlank(),
                    'url'   => new Assert\Url(),
                ),
                'allowExtraFields' => true,
            ))
        ));
    }
}