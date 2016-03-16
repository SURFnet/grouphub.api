<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use AppBundle\Entity\UserInGroup;
use AppBundle\Form\DataTransformer\EntityToIdTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserInGroupType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            $builder->create(
                'user',
                'text',
                [
                    'constraints' => new NotBlank()
                ]
            )->addModelTransformer(new EntityToIdTransformer($this->manager, User::class))
        )->add(
            'role',
            'choice',
            [
                'constraints'       => new NotBlank(),
                'choices'           => [UserInGroup::ROLE_ADMIN, UserInGroup::ROLE_MEMBER, UserInGroup::ROLE_PROSPECT],
                'choices_as_values' => true,
            ]
        )->add(
            'message',
            'text',
            [
                'mapped' => false,
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
