<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            'text',
            [
                'constraints' => new NotBlank(),
            ]
        )->add(
            'description',
            'text'
        )->add(
            'type',
            'text',
            [
                'constraints' => new NotBlank(),
            ]
        )->add(
            'reference',
            'text'
        )->add(
            'owner',
            'entity',
            [
                'class'        => 'AppBundle\Entity\User',
                'choice_label' => 'id',
                'constraints'  => new NotBlank(),
            ]
        )->add(
            'parent',
            'entity',
            [
                'class'        => 'AppBundle\Entity\UserGroup',
                'choice_label' => 'id',
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'         => 'AppBundle\Entity\UserGroup',
                'intention'          => 'group',
                'translation_domain' => 'AppBundle',
            ]
        );
    }

    public function getName()
    {
        return 'group';
    }
}
