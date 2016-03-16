<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * @ORM\Entity
 * @ExclusionPolicy("all")
 */
class Notification
{
    const TYPE_PROSPECT = 'prospect';

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Expose()
     */
    protected $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(referencedColumnName="UserId", onDelete="CASCADE")
     */
    protected $to;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(referencedColumnName="UserId", onDelete="CASCADE")
     *
     * @Expose()
     */
    protected $from;

    /**
     * @var UserGroup
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserGroup")
     * @ORM\JoinColumn(referencedColumnName="UserGroupId", onDelete="CASCADE", nullable=true)
     *
     * @Expose()
     */
    protected $group;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @Expose()
     */
    protected $message;

    /**
     * @var string
     *
     * @ORM\Column()
     *
     * @Expose()
     */
    protected $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     *
     * @Expose()
     */
    protected $created;

    /**
     * @param User      $to
     * @param User      $from
     * @param string    $type
     * @param string    $message
     * @param UserGroup $group
     */
    public function __construct(
        User $to,
        User $from,
        $type = self::TYPE_PROSPECT,
        $message = '',
        UserGroup $group = null
    ) {
        $this->to = $to;
        $this->from = $from;
        $this->type = $type;
        $this->message = $message;
        $this->group = $group;

        $this->created = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
