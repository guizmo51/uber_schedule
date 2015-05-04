<?php

class AuthenticationSuccessListener
{

/**
 * @param AuthenticationSuccessEvent $event
 */
public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
{
    $data = $event->getData();
    $user = $event->getUser();
    var_dump("la");
    if (!$user instanceof UserInterface) {
        return;
    }

    // $data['token'] contains the JWT

    $data['data'] = array(
        'roles' => $user->getRoles(),
    );
    
    $event->setData($data);
}

}