<?php

namespace AppBundle\Form;

use AppBundle\Entity\UserGroup;
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
            'choice',
            [
                'constraints'       => new NotBlank(),
                'choices'           => [UserGroup::TYPE_GROUPHUB, UserGroup::TYPE_FORMAL, UserGroup::TYPE_LDAP],
                'choices_as_values' => true,
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
