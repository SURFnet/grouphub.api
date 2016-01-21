<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserGroupInGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'groupInGroup',
            'entity',
            [
                'class'        => 'AppBundle\Entity\UserGroup',
                'choice_label' => 'id',
                'constraints'  => new NotBlank(),
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'         => 'AppBundle\Entity\UserGroupInGroup',
                'intention'          => 'groupInGroup',
                'translation_domain' => 'AppBundle',
            ]
        );
    }

    public function getName()
    {
        return 'groupInGroup';
    }
}
