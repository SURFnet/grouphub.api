<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use AppBundle\Entity\UserGroup;
use AppBundle\Entity\UserInGroup;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadGroupData
 */
class LoadGroupData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $owner = new User();
        $owner->setId(2);

        $group = new UserGroup();
        $group->setId(1);
        $group->setName('AdminGroup');
        $group->setDescription('GroupHUB administrator group');
        $group->setType('formal');
        $group->setOwner($this->getReference('formal-user'));
        $group->setParent(null);
        $group->setTimestamp(new \DateTime());
        $group->setReference('sys:formal_group');
        $group->setActive(1);
        $manager->persist($group);

        $membership = new UserInGroup();
        $membership->setGroup($group);
        $membership->setUser($this->getReference('admin-user'));
        $membership->setRole('admin');
        $manager->persist($membership);

        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 2;
    }
}
