<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadUserData
 */
class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @inheritdoc
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setId(1);
        $user->setFirstName('Ldap');
        $user->setLastName('User');
        $user->setLoginName('LdapUser');
        $user->setTimeStamp(new \DateTime());
        $user->setReference('sys:ldap_user');
        $user->setType('system');
        $manager->persist($user);

        $user = new User();
        $user->setId(2);
        $user->setFirstName('Formal');
        $user->setLastName('User');
        $user->setLoginName('FormalUser');
        $user->setTimeStamp(new \DateTime());
        $user->setReference('sys:formal_user');
        $user->setType('system');
        $manager->persist($user);

        $this->addReference('formal-user', $user);

        $user = new User();
        $user->setId(3);
        $user->setFirstName('Trash');
        $user->setLastName('User');
        $user->setLoginName('TrashUser');
        $user->setTimeStamp(new \DateTime());
        $user->setReference('sys:trash_user');
        $user->setType('system');
        $manager->persist($user);

        $user = new User();
        $user->setId(4);
        $user->setFirstName('Ad');
        $user->setLastName('Min');
        $user->setLoginName($this->container->getParameter('admin_uid'));
        $user->setTimeStamp(new \DateTime());
        $user->setReference($this->container->getParameter('admin_dn'));
        $user->setType('ldap');
        $manager->persist($user);

        $this->addReference('admin-user', $user);

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
