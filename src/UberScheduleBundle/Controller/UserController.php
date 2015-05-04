<?php

namespace UberScheduleBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use UberScheduleBundle\Entity\User;
use UberScheduleBundle\Form\UserType;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Stevenmaguire\Uber\Client;
use \DateTime;
use \DateInterval;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\HttpFoundation\Session\Session;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\View\View,
    FOS\RestBundle\View\ViewHandler,
    FOS\RestBundle\View\RouteRedirectView;
/**
 * User controller.
 *
 * @Route("/user")
 */
class UserController extends Controller implements ClassResourceInterface
{
  
    public function uberRedirectAction() {
   
        $conf = $this->container->getParameter('uberClient');
        $clientSecret = $conf['secretClient'];
        $clientId = $conf['idClient'];
        $serverToken = $conf['serverToken'];
        $backUrl = $this->container->getParameter('back_url');
        $request = Request::createFromGlobals();
        $authCode = $request->query->get('code');

        $curlOptions = array(
                        CURLOPT_RETURNTRANSFER => TRUE,
                        CURLOPT_FOLLOWLOCATION => TRUE,
                        CURLOPT_VERBOSE => TRUE,
                        CURLOPT_SAFE_UPLOAD=>FALSE
                        );
        
        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            
        $data = array('client_secret'=>$clientSecret,
            'client_id'=>$clientId,
            'grant_type'=>'authorization_code',
            'redirect_uri'=>$backUrl.'/app_dev.php/user/uber/f6d75c949cda2517b826cacba5523792',
            'code'=>$authCode);
       
        curl_setopt ($ch, CURLOPT_POST, true); 
        curl_setopt ($ch, CURLOPT_URL,'https://login.uber.com/oauth/token'); 
        curl_setopt ($ch, CURLOPT_POSTFIELDS,http_build_query($data)); 

        $result = curl_exec($ch);

        if($dataJson = json_decode($result, true)){

            if(isset($dataJson['error'])){

                throw new AccessDeniedException("Your code expired, please log in again");
                
            }
            
            if(isset($dataJson['access_token'])){
               
                $client = new Client(array(
                    'access_token' => $dataJson['access_token'],
                    'server_token' => $serverToken,
                    'use_sandbox'  => true, // optional, default false
                    'version'      => 'v1', // optional, default 'v1'
                    'locale'       => 'en_US', // optional, default 'en_US'
                ));    

                // Load profile from UBER
                $profile = $client->getProfile();
                
                $em = $this->getDoctrine()->getManager();

                $userRepository = $em->getRepository('UberScheduleBundle:User');
                $userFind = $userRepository->findByUuid($profile->uuid);
                
                if(empty($userFind)){
                    $user = new User();
                    $user->setCreatedAt(new Datetime);
                } else {
                    $user = $userFind[0];
                }
                
                /* Data from profile API */
                $user->setEmail($profile->email);
                $user->setUuid($profile->uuid);
                $user->setFirstName($profile->first_name);
                $user->setLastName($profile->last_name);
                $user->setPromoCode($profile->promo_code);
                $user->setEmail($profile->email);
                $user->setPicture($profile->picture);
                $user->setUpdatedAt(new Datetime);

                /* Set password */ 
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $password = uniqid('',true);
                $user->setPassword($encoder->encodePassword($password,$user->getSalt()));

                /* Data from auth */
                $user->setAccessToken($dataJson['access_token']);
                $user->setRefreshToken($dataJson['refresh_token']);

                $now = new Datetime;
                $now->add(new DateInterval('PT'.intval($dataJson['expires_in']).'S'));
                $user->setTokenExpiration($now);

                 $em->persist($user);
                
                $em->flush();
                
                $this->container->getParameter('front_url');
                $fullUrl = $this->container->getParameter('back_url').$this->generateUrl('get_jwt_token', array('username'=>$profile->uuid, 'password'=>$password));

              return $this->redirect("http://localhost:9002/#/login#".base64_encode($fullUrl));
            
            }
        }
    
    }



    public function redirectAction(){
        // TODO changer url
        return $this->redirect("https://login.uber.com/oauth/authorize?client_id=rA_xJyeKF3srdRgdoGYlfPLG6eLA-uno&response_type=code&scope=profile%20history_lite%20history%20request", 301);
    }

    /**
     * Lists all User entities.
     *
     * @Route("/", name="user")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $session = new Session();
        $session->start();
                   
    }
    /**
     * Lists all User entities.
     *
     * @Route("/info", name="user")
     * @Method("GET")
     * @Template()
     * GET Route annotation.
     * @Get("/user/info")
     */ 
    public function getInfoAction(){

        $session = new Session;
        $session->start();
       
         $view = View::create()
         ->setStatusCode(200)
         ->setData($this->get('security.context')->getToken()->getUser())
         ->setFormat('json')   // <- format here
    ;
    return $this->get('fos_rest.view_handler')->handle($view);
    }
    /**
     * Creates a new User entity.
     *
     * @Route("/", name="user_create")
     * @Method("POST")
     * @Template("UberScheduleBundle:User:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('user_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('user_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/new", name="user_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new User();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="user_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UberScheduleBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="user_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UberScheduleBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a User entity.
    *
    * @param User $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('user_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}", name="user_update")
     * @Method("PUT")
     * @Template("UberScheduleBundle:User:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UberScheduleBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('user_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a User entity.
     *
     * @Route("/{id}", name="user_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('UberScheduleBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('user'));
    }

    /**
     * Creates a form to delete a User entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    public function getActivityAction(){
        $serializer = $this->get('serializer');
    $session = new Session;
        $session->start();
        $userData = $session->get('user');

        $conf = $this->container->getParameter('uberClient');
        $serverToken = $conf['serverToken'];
        
        $client = new Client(array(
          'access_token' => $this->get('security.context')->getToken()->getUser()->getAccessToken(),
          'server_token' => $serverToken,
          'use_sandbox'  => true, // optional, default false
          'version'      => 'v1', // optional, default 'v1'
          'locale'       => 'en_US', // optional, default 'en_US'
        ));    

        $client->setVersion('v1.1');
        $history = $client->getHistory(array(
    'limit' => 50, // optional
    'offset' => 0 // optional
));

        $view = View::create()
         ->setStatusCode(200)
         ->setData(json_decode(json_encode($history),TRUE))
         ->setFormat('json')   // <- format here
    ;
    return $this->get('fos_rest.view_handler')->handle($view);
    }

    public function getProfileAction(){

       
         $serializer = $this->get('serializer');
    $session = new Session;
        $session->start();
        $userData = $session->get('user');

        $conf = $this->container->getParameter('uberClient');
        $serverToken = $conf['serverToken'];
        
        $client = new Client(array(
          'access_token' => $userData->getAccessToken(),
          'server_token' => $serverToken,
          'use_sandbox'  => true, // optional, default false
          'version'      => 'v1', // optional, default 'v1'
          'locale'       => 'en_US', // optional, default 'en_US'
        ));    

        $profile = $client->getProfile();

         $view = View::create()
         ->setStatusCode(200)
         ->setData(json_decode(json_encode($profile),TRUE))
         ->setFormat('json')   // <- format here
    ;
    return $this->get('fos_rest.view_handler')->handle($view); 
    }

}
