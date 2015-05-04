<?php

namespace UberScheduleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\Controller\Annotations\Get;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Stevenmaguire\Uber\Client;
use Symfony\Component\Validator\Constraints\DateTime;
use FOS\RestBundle\View\View,
    FOS\RestBundle\View\ViewHandler,
    FOS\RestBundle\View\RouteRedirectView;

class RequestController extends Controller 
{

public function refreshRequestsAction(){

  $conf = $this->container->getParameter('uberClient');
        $serverToken = $conf['serverToken'];

  $client = new Client(array(
          'access_token' => $this->get('security.context')->getToken()->getUser()->getAccessToken(),
          'server_token' => $serverToken,
          'use_sandbox'  => $conf['useSandbox'], // optional, default false
          'version'      => 'v1', // optional, default 'v1'
          'locale'       => 'en_US', // optional, default 'en_US'
        ));    

        $client->setVersion('v1.2');
        $history = $client->getHistory(array(
    'limit' => 50, // optional
    'offset' => 0 // optional
      ));
        $em = $this->getDoctrine()->getManager();
        $requests= $em->getRepository('UberScheduleBundle:Request')->findByUser($this->get('security.context')->getToken()->getUser());
        
        foreach($requests as $request) {

        }
        $products = [];
        $client->setVersion('v1');

        // Parse products
        foreach($history->history as $request) {
          $product = $client->getProduct($request->product_id);
          $products[$request->product_id] = $product->display_name;
        }
        // parse product
        foreach($requests as $request) {
          $product =  $client->getProduct($request->getProductId());
          $products[$request->getProductId()] = $product->display_name;
        }
$data = array('uber_requests' =>json_decode(json_encode($history->history),TRUE),
'products' => $products , 'BCUrequests' => $requests);
  
          $view = View::create()
         ->setStatusCode(200)
         ->setData($data)
         ->setFormat('json')   // <- format here
    ;
    return $this->get('fos_rest.view_handler')->handle($view);
}


public function getTokenAction()
{
    // The security layer will intercept this request

    
        $security = $this->get('security.context');
        $providerKey = $this->container->getParameter('fos_user.firewall_name');
        $roles = $user->getRoles();

        //$user->

        $token = new UsernamePasswordToken($user, null, $providerKey, $roles);
        $security->setToken($token);

    return new Response('', 404);
}

	/**
	 * GET Route annotation.
	 * @Get("/products/{geo}")
	 */
  public function editRequestAction($requestId, $status){
    
    $conf = $this->container->getParameter('uberClient');
    $serializer = $this->get('serializer');
    $conf = $this->container->getParameter('uberClient');
    $curl = curl_init($conf['sandboxUrl']. "v1/sandbox/requests/".$requestId);
    
$data = array(
  'status' => $status,
  );
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($curl, CURLOPT_HEADER, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Length: '.strlen(json_encode($data)).'','Content-Type: application/json',"Authorization: Bearer ".$this->get('security.context')->getToken()->getUser()->getAccessToken()));
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

// Make the REST call, returning the result
$response = curl_exec($curl);
if (!$response) {
    die("Connection Failure.n");
}
  }

	public function getProductsAction($lat, $lon){
		    $serializer = $this->get('serializer');
	
$userData = $this->get('security.context')->getToken()->getUser();
        $conf = $this->container->getParameter('uberClient');
        $serverToken = $conf['serverToken'];

        $client = new Client(array(
          'access_token' => $userData->getAccessToken(),
          'server_token' => $serverToken,
          'use_sandbox'  => $conf['useSandbox'], // optional, default false
          'version'      => 'v1', // optional, default 'v1'
          'locale'       => 'en_US', // optional, default 'en_US'
        ));       
              $products = $client->getProducts(array(
    'latitude' => (string)$lat,
    'longitude' => (string)$lon
));

               $view = View::create()
         ->setStatusCode(200)
         ->setData(json_decode(json_encode($products),TRUE))
         ->setFormat('json')   // <- format here
    ;
    return $this->get('fos_rest.view_handler')->handle($view);
	}

  public function getProductAction($pid) {

    $serializer = $this->get('serializer');
    $session = new Session;
        $session->start();
        $userData = $session->get('user');

        $conf = $this->container->getParameter('uberClient');
        $serverToken = $conf['serverToken'];

        $client = new Client(array(
          'access_token' => $userData->getAccessToken(),
          'server_token' => $serverToken,
          'use_sandbox'  => $conf['useSandbox'], // optional, default false
          'version'      => 'v1', // optional, default 'v1'
          'locale'       => 'en_US', // optional, default 'en_US'
        ));       
              $product = $client->getProduct($pid);

               $view = View::create()
         ->setStatusCode(200)
         ->setData(json_decode(json_encode($product),TRUE))
         ->setFormat('json')   // <- format here
    ;
    return $this->get('fos_rest.view_handler')->handle($view);

  }

  public function getPriceEstimatesAction($startLat, $startLon, $endLat, $endLon) {

    $serializer = $this->get('serializer');
        

        $conf = $this->container->getParameter('uberClient');
        $serverToken = $conf['serverToken'];
        
        $client = new Client(array(
          'access_token' => $this->get('security.context')->getToken()->getUser()->getAccessToken(),
          'server_token' => $serverToken,
          'use_sandbox'  => $conf['useSandbox'], // optional, default false
          'version'      => 'v1', // optional, default 'v1'
          'locale'       => 'en_US', // optional, default 'en_US'
        ));       
              $product = $client->getPriceEstimates(array(
    'start_latitude' => (string)$startLat,
    'start_longitude' => (string)$startLon,
    'end_latitude' => (string)$endLat,
    'end_longitude' => (string)$endLon
));

               $view = View::create()
         ->setStatusCode(200)
         ->setData(json_decode(json_encode($product),TRUE))
         ->setFormat('json')   // <- format here
    ;
    return $this->get('fos_rest.view_handler')->handle($view);

  }

public function getTimeEstimatesAction($startLat, $startLon) {
  $serializer = $this->get('serializer');
    $session = new Session;
        $session->start();
        $userData = $session->get('user');

        $conf = $this->container->getParameter('uberClient');
        $serverToken = $conf['serverToken'];
        
        $client = new Client(array(
          'access_token' => $userData->getAccessToken(),
          'server_token' => $serverToken,
          'use_sandbox'  => $conf['useSandbox'], // optional, default false
          'version'      => 'v1', // optional, default 'v1'
          'locale'       => 'en_US', // optional, default 'en_US'
        ));       
  $estimates = $client->getTimeEstimates(array(
     'start_latitude' => (string)$startLat,
    'start_longitude' => (string)$startLon,
));

  $view = View::create()
         ->setStatusCode(200)
         ->setData(json_decode(json_encode($estimates),TRUE))
         ->setFormat('json')   // <- format here
    ;
    return $this->get('fos_rest.view_handler')->handle($view);

}

public function getPromotionsAction($startLat, $startLon, $endLat, $endLon) {
  $serializer = $this->get('serializer');
    $session = new Session;
        $session->start();
        $userData = $session->get('user');

        $conf = $this->container->getParameter('uberClient');
        $serverToken = $conf['serverToken'];
        
        $client = new Client(array(
          'access_token' => $userData->getAccessToken(),
          'server_token' => $serverToken,
          'use_sandbox'  => $conf['useSandbox'], // optional, default false
          'version'      => 'v1', // optional, default 'v1'
          'locale'       => 'en_US', // optional, default 'en_US'
        ));       
  $promotions = $client->getPromotions(array(
    'start_latitude' => (string)$startLat,
    'start_longitude' => (string)$startLon,
    'end_latitude' => (string)$endLat,
    'end_longitude' => (string)$endLon
));

  $view = View::create()
         ->setStatusCode(200)
         ->setData(json_decode(json_encode($promotions),TRUE))
         ->setFormat('json')   // <- format here
    ;
    return $this->get('fos_rest.view_handler')->handle($view);

}

public function rideAction(Request $request){
  $serializer = $this->get('serializer');
        $conf = $this->container->getParameter('uberClient');
        $serverToken = $conf['serverToken'];
        $client = new Client(array(
          'access_token' => $this->get('security.context')->getToken()->getUser()->getAccessToken(),
          'server_token' => $serverToken,
          'use_sandbox'  => $conf['useSandbox'], // optional, default false
          'version'      => 'v1', // optional, default 'v1'
          'locale'       => 'en_US', // optional, default 'en_US'
        ));  
 
$dataFromForm = json_decode($request->getContent(), true);
$data['product_id'] = $dataFromForm['product']['product_id'];
$data['start_lat'] = $dataFromForm['where']['start']['lat'];
$data['start_lon'] = $dataFromForm['where']['start']['lon'];
$data['end_lat'] = $dataFromForm['where']['end']['lat'];
$data['end_lon'] = $dataFromForm['where']['end']['lon'];


// If user request a uber now
if($dataFromForm['when'] == "now"){

  $request = $client->requestRide(array(
      'product_id' => $data['product_id'],
      'start_latitude' => (string)$data['start_lat'],
      'start_longitude' => (string)$data['start_lon'],
      'end_latitude' => (string)$data['end_lat'],
      'end_longitude' => (string)$data['end_lon']
  ));

  $response = json_decode(json_encode($request),TRUE);

  if ($response['status'] == "processing"){

    $request = new \UberScheduleBundle\Entity\Request();
   $em = $this->getDoctrine()->getManager();

    $request->setUser($this->container->get('security.context')->getToken()->getUser());
    $request->setMode('now');
    $request->setStatus($response['status']);
    $request->setRequestTimeUTC(new \DateTime('NOW'));
    $request->setProductId($data['product_id']);
    $request->setRequestId($response['request_id']);
    $request->setStartLat($data['start_lat']);
    $request->setStartLon($data['start_lon']);
    $request->setEndLat($data['end_lat']);
    $request->setEndLon($data['end_lon']);
    $em->persist($request);
    $em->flush();
  }    

} else if ($dataFromForm['when'] == "later") {
  // We only put the request in DB 
  $request = new \UberScheduleBundle\Entity\Request();
  $em = $this->getDoctrine()->getManager();

  $request->setUser($this->container->get('security.context')->getToken()->getUser());
  $request->setMode('later');

  $dateInput = $dataFromForm['details']['date'];
  $date = new \DateTime($dateInput,new \DateTimeZone($dataFromForm['where']['timezoneId']));
  $request->setStatus("saved");
  $request->setTz($dataFromForm['where']['timezoneId']);
$date->setTimezone(new \DateTimeZone('UTC'));
  $request->setRequestTimeUTC($date);
  $request->setProductId($data['product_id']);
  $request->setStartLat($data['start_lat']);
  $request->setStartLon($data['start_lon']);
  $request->setEndLat($data['end_lat']);
  $request->setEndLon($data['end_lon']);
  $em->persist($request);
  $em->flush();
  $response = array('ok');
} else if($dataFromForm['when'] == "trigger"){

}
 $view = View::create()
         ->setStatusCode(200)
         ->setData($response)
         ->setFormat('json')   // <- format here
    ;
    return $this->get('fos_rest.view_handler')->handle($view);

}
public function rideDetailsAction($requestId){
 $serializer = $this->get('serializer');
    
        $conf = $this->container->getParameter('uberClient');
        $serverToken = $conf['serverToken'];
        
        $client = new Client(array(
          'access_token' =>$this->get('security.context')->getToken()->getUser()->getAccessToken(),
          'server_token' => $serverToken,
          'use_sandbox'  => $conf['useSandbox'], // optional, default false
          'version'      => 'v1', // optional, default 'v1'
          'locale'       => 'en_US', // optional, default 'en_US'
        ));   
          
        $request = $client->getRequest($requestId);
 $view = View::create()
         ->setStatusCode(200)
         ->setData(json_decode(json_encode($request),TRUE))
         ->setFormat('json')   // <- format here
    ;
    return $this->get('fos_rest.view_handler')->handle($view);

}

public function mapAction($requestId){
 $serializer = $this->get('serializer');
    $session = new Session;
        $session->start();
        $userData = $session->get('user');

        $conf = $this->container->getParameter('uberClient');
        $serverToken = $conf['serverToken'];
        
        $client = new Client(array(
          'access_token' => $this->get('security.context')->getToken()->getUser()->getAccessToken(),
          'server_token' => $serverToken,
          'use_sandbox'  => $conf['useSandbox'], // optional, default false
          'version'      => 'v1', // optional, default 'v1'
          'locale'       => 'en_US', // optional, default 'en_US'
        ));   
          
        $request = $client->getRequestMap($requestId);
  

 $view = View::create()
         ->setStatusCode(200)
         ->setData(json_decode(json_encode($request),TRUE))
         ->setFormat('json')   // <- format here
    ;
    
    return $this->get('fos_rest.view_handler')->handle($view);

}

public function rideCancelAction($requestId) {
  $serializer = $this->get('serializer');
    $session = new Session;
        $session->start();
        $userData = $session->get('user');

        $conf = $this->container->getParameter('uberClient');
        $serverToken = $conf['serverToken'];
        
        $client = new Client(array(
          'access_token' => $this->get('security.context')->getToken()->getUser()->getAccessToken(),
          'server_token' => $serverToken,
          'use_sandbox'  => $conf['useSandbox'], // optional, default false
          'version'      => 'v1', // optional, default 'v1'
          'locale'       => 'en_US', // optional, default 'en_US'
        ));   
          
        $request = $client->cancelRequest($requestId);
  

 $view = View::create()
         ->setStatusCode(200)
         ->setData(json_decode(json_encode($request),TRUE))
         ->setFormat('json')   // <- format here
    ;
    return $this->get('fos_rest.view_handler')->handle($view);

}

}
