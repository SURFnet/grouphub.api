<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class UserAnnotation
 *
 * @ORM\Entity
 * @ORM\Table(name="UserAnnotation")
 */
class UserAnnotation
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\Column(name="AnnotationId", type="integer", length=11, nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="AnnotationAttribute", type="string", length=256, nullable=true)
     */
    protected $attribute;

    /**
     * @var string
     * @ORM\Column(name="AnnotationValue", type="string", length=4096, nullable=true)
     */
    protected $value;

    /**
     * @var string
     * @ORM\Column(name="AnnotationType", type="string", length=128, nullable=true)
     */
    protected $type;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
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
