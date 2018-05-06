<?php
/**
 * Created by PhpStorm.
 * User: designerbg19
 * Date: 25/04/2018
 * Time: 15:09
 */

namespace AppBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RegistrationListener implements EventSubscriberInterface
{
public  static  function  getSubscribedEvents()
{
    return array(
        FOSUserEvents::REGISTRATION_SUCCESS=>'onRegistrationSuccess'
    );
    // TODO: Implement getSubscribedEvents() method.
}

public  function  onRegistrationSuccess(FormEvent $event){

    $roles =array('ROLE_BIDON');
    $user =$event->getForm()->getData();
    $user->setRoles($roles);

}

}