<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use AppBundle\Entity\UserGroup;
use AppBundle\Form\DataTransformer\EntityToIdTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserGroupType extends AbstractType
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
            $builder->create(
                'owner',
                'text',
                [
                    'constraints' => new NotBlank(),
                ]
            )->addModelTransformer(new EntityToIdTransformer($this->manager, User::class))
        )->add(
            $builder->create(
                'parent',
                'text'
            )->addModelTransformer(new EntityToIdTransformer($this->manager, UserGroup::class))
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
