<?php

namespace AppBundle\Form;

use AppBundle\Entity\UserGroup;
use AppBundle\Form\DataTransformer\EntityToIdTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserGroupInGroupType extends AbstractType
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
                'groupInGroup',
                'text',
                [
                    'constraints' => new NotBlank()
                ]
            )->addModelTransformer(new EntityToIdTransformer($this->manager, UserGroup::class))
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
