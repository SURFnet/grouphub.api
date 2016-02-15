<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Class UserAnnotation
 *
 * @ORM\Entity
 * @ORM\Table(name="UserAnnotation")
 *
 * @ExclusionPolicy("all")
 */
class UserAnnotation
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(name="AnnotationId", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="AnnotationAttribute", type="string", nullable=true)
     *
     * @Expose()
     */
    protected $attribute;

    /**
     * @var string
     *
     * @ORM\Column(name="AnnotationValue", type="string", nullable=true)
     *
     * @Expose()
     */
    protected $value;

    /**
     * @var string
     *
     * @ORM\Column(name="AnnotationType", type="string", nullable=true)
     */
    protected $type;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="annotations")
     * @ORM\JoinColumn(name="UserId", referencedColumnName="UserId", onDelete="CASCADE")
     */
    protected $user;

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @param string $attribute
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
