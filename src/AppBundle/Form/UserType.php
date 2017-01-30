<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('displayName', TextType::class)
            ->add('loginName', TextType::class, ['constraints' => new NotBlank()])
            ->add('reference', TextType::class, ['constraints' => new NotBlank(),])
            ->add(
                'annotations',
                CollectionType::class,
                [
                    'type' => new UserAnnotationType(),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
                'intention' => 'user',
                'translation_domain' => 'AppBundle',
            ]
        );
    }

    public function getName()
    {
        return 'user';
    }
}
