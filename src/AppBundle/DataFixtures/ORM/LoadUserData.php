<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;

/**
 * Class LoadUserData
 */
class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setId(1);
        $user->setFirstName('Ldap');
        $user->setLastName('User');
        $user->setLoginName('LdapUser');
        $user->setTimeStamp(new\DateTime());
        $user->setReference('sys:ldap_user');
        $user->setType('system');
        $manager->persist($user);

        $user = new User();
        $user->setId(2);
        $user->setFirstName('Formal');
        $user->setLastName('User');
        $user->setLoginName('FormalUser');
        $user->setTimeStamp(new\DateTime());
        $user->setReference('sys:formal_user');
        $user->setType('system');
        $manager->persist($user);

        $this->addReference('formal-user', $user);

        $user = new User();
        $user->setId(3);
        $user->setFirstName('Trash');
        $user->setLastName('User');
        $user->setLoginName('TrashUser');
        $user->setTimeStamp(new\DateTime());
        $user->setReference('sys:trash_user');
        $user->setType('system');
        $manager->persist($user);

        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 1;
    }
}
