<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserInGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $notBlank = [ "constraints" => new NotBlank() ];

        $builder
            ->add('userId', 'integer', $notBlank)
            ->add('groupId', 'integer', $notBlank)
            ->add('role', 'text', [ "required" => true ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'         => 'AppBundle\Entity\UserInGroup',
            'translation_domain' => 'AppBundle'
        ));
    }

    public function getName()
    {
        return 'userInGroup';
    }
}
