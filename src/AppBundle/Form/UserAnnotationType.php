<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserAnnotationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'key',
            'text',
            [
                'constraints'   => new NotBlank(),
                'property_path' => 'attribute',
            ]
        )->add(
            'value',
            'text',
            [
                'constraints' => new NotBlank(),
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'AppBundle\Entity\UserAnnotation',
            ]
        );
    }

    public function getName()
    {
        return 'user_annotation';
    }
}
