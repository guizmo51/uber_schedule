<?php

namespace UberScheduleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
    	$uberClient = $this->container->get('guzzle.uber.client');
        $status = $uberClient->getProducts(array('latitude'=>37.7759792,'longitude'=>-122.41823));

 	var_dump($uberClient->getEstimatesPrice(array('start_latitude'=>48.844327,'start_longitude'=>2.244955,'end_latitude'=>48.859008,'end_longitude'=>2.417346)));
        return $this->render('UberScheduleBundle:Default:index.html.twig', array('name' => $name));
    }
}
