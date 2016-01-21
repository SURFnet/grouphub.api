<?php

namespace AppBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserInGroupUpdateType extends UserInGroupType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'role',
            'text',
            [
                'constraints' => new NotBlank(),
            ]
        );
    }
}
