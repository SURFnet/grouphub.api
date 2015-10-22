<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as orm;

/**
 * Class UserAnnotation
 * @package AppBundle\Entity
 * @orm\Entity
 * @orm\Table(name="UserAnnotation")
 */
class UserAnnotation
{
    /**
     * @var int
     * @orm\Column(name="AnnotationAttribute", type="integer", length=11)
     */
    protected $id;

    /**
     * @var string
     * @orm\Column(name="AnnotationAttribute", type="string", length=256)
     */
    protected $attribute;

    /**
     * @var string
     * @orm\Column(name="AnnotationValue", type="string", length=4096)
     */
    protected $value;

    /**
     * @var string
     * @orm\Column(name="AnnotationType", type="string", length=128)
     */
    protected $type;

    /**
     * @var int
     * @orm\Column(name="UserId", type="integer", length=11)
     */
    protected $userId;

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
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