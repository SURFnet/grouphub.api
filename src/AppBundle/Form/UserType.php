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
        $notBlank = [ "constraints" => new NotBlank() ];

        $builder
            ->add('firstName', 'text')
            ->add('lastName', 'text')
            ->add('loginName', 'text', $notBlank)
            ->add('reference', 'text', $notBlank);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'         => 'AppBundle\Entity\User',
            'intention'          => 'user',
            'translation_domain' => 'AppBundle'
        ));

    }

    public function getName()
    {
        return 'user';
    }
}
