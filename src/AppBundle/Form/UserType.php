<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'firstName',
            'text'
        )->add(
            'lastName',
            'text'
        )->add(
            'loginName',
            'text',
            [
                'constraints' => new NotBlank(),
            ]
        )->add(
            'reference',
            'text',
            [
                'constraints' => new NotBlank(),
            ]
        )->add(
            'annotations',
            'collection',
            [
                'type'         => new UserAnnotationType(),
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'         => 'AppBundle\Entity\User',
                'intention'          => 'user',
                'translation_domain' => 'AppBundle',
            ]
        );
    }

    public function getName()
    {
        return 'user';
    }
}
