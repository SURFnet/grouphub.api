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
        $notBlank = [ "constraints" => new NotBlank() ];

        $builder
            ->add('groupId', 'integer', $notBlank)
            ->add('groupInGroupId', 'integer', $notBlank);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'         => 'AppBundle\Entity\UserGroupInGroup',
            'intention'          => 'groupInGroup',
            'translation_domain' => 'AppBundle'
        ));
    }

    public function getName()
    {
        return 'groupInGroup';
    }
}
