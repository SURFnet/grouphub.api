<?php

namespace AppBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class EntityToIdTransformer
 */
class EntityToIdTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var string
     */
    private $entityName;

    /**
     * @param ObjectManager $om
     * @param string        $entityName
     */
    public function __construct(ObjectManager $om, $entityName)
    {
        $this->entityName = $entityName;
        $this->om = $om;
    }

    /**
     * Transforms an object to a string.
     *
     * @param object|null $entity
     *
     * @return string
     */
    public function transform($entity)
    {
        if (null === $entity) {
            return '';
        }

        return $entity->getId();
    }

    /**
     * Transforms a string to an object.
     *
     * @param string $id
     *
     * @return object|null
     *
     * @throws TransformationFailedException if object is not found.
     */
    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }

        $entity = $this->om->getRepository($this->entityName)->find($id);

        if (null === $entity) {
            throw new TransformationFailedException(sprintf('An entity with id "%s" does not exist!', $id));
        }

        return $entity;
    }
}
