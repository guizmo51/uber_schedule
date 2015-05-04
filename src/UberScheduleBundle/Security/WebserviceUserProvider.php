<?php

namespace UberScheduleBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use UberScheduleBundle\Entity\User;
use Doctrine\ORM\EntityManager;

class WebserviceUserProvider implements UserProviderInterface
{
private $service;
private $em;
public function __construct(EntityManager $em) {

	$this->em = $em;
	$this->userRepo = $this->em->getRepository('UberScheduleBundle:User');
}

public function loadUserByUsername($username)
{
// Do we have a local record?
	
	
if ($user = $this->userRepo->findOneBy(array('uuid' => $username))) {

return $user;

}

// Try service


throw new UsernameNotFoundException(sprintf('No record found for user %s', $username));
}

public function refreshUser(UserInterface $user)
{
return $this->loadUserByUsername($user->getUuid());
}

public function supportsClass($class)
{
return $class === 'UberScheduleBundle\Entity\User';
}

protected function findUserBy(array $criteria)
{
$repository = $this->em->getRepository('UberScheduleBundle\Entity\User');
return $repository->findOneBy($criteria);
}
}