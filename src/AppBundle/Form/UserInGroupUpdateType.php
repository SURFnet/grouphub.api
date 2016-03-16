<?php

namespace AppBundle\Form;

use AppBundle\Entity\UserInGroup;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserInGroupUpdateType extends UserInGroupType
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
}
