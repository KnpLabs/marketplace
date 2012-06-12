<?php

namespace Marketplace\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectType extends AbstractType
{
    public function getName()
    {
        return 'project';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text');
        $builder->add('description', 'textarea');
        $builder->add('category', 'choice', array('choices' => $options['categories']));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'categories' => function (Options $options, $value) {
                return array_merge(array('none' => 'No category'), $value);
            },
            'validation_constraint' => function(Options $options, $value) {
                return new Assert\Collection(array(
                    'fields' => array(
                        'name'        => new Assert\NotBlank(),
                        'description' => new Assert\NotBlank(),
                        'category'    => new Assert\Choice(array(
                            'choices' => array_keys($options['categories'])
                        ))
                    ),
                    'allowExtraFields' => true,
                ));
            }
        ));
    }
}