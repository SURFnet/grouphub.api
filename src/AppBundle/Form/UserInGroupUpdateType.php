<?php

namespace AppBundle\Form;

use AppBundle\Entity\UserInGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserInGroupUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'role',
            'choice',
            [
                'constraints'       => new NotBlank(),
                'choices'           => [UserInGroup::ROLE_ADMIN, UserInGroup::ROLE_MEMBER, UserInGroup::ROLE_PROSPECT],
                'choices_as_values' => true,
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'         => 'AppBundle\Entity\UserInGroup',
                'translation_domain' => 'AppBundle',
            ]
        );
    }

    public function getName()
    {
        return 'userInGroup';
    }
}
