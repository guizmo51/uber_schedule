<?php

namespace UberScheduleBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Stevenmaguire\Uber\Client;
use GuzzleHttp\Ring\Exception\ConnectException;
use UberScheduleBundle\Entity\Request;
class RequestCommand extends ContainerAwareCommand {


	protected function configure()
    {
        $this
            ->setName('uberschedule:request:launch')
            ->setDescription('Launch the requests flagged as "saved" and past time')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output){
    	$conf = $this->getContainer()->getParameter('uberClient');
        $clientSecret = $conf['secretClient'];
        $clientId = $conf['idClient'];
        $serverToken = $conf['serverToken'];

    	$qb = $this->getContainer()->get('Doctrine')->getManager()->createQueryBuilder();
    	$qb->select('user.id','user.email, user.first_name, user.last_name, user.access_token, user.refresh_token, r.product_id, r.start_lat, r.start_lon, r.end_lat, r.end_lon, r.id as rid')
    		->from('UberScheduleBundle\Entity\Request', 'r')
			->join('r.user', 'user')
    		->where('r.status = \'saved\'' )
    		->andWhere('r.requestTimeUTC < :requestTimeUTC')
    		->setParameter('requestTimeUTC', date('Y-m-d H:i:s'));

    		$requests = $qb->getQuery()->getArrayResult();

    	
    	foreach ($requests as $request) {

    		unset($client);

    		$client = new Client(array(
          		'access_token' => $request['access_token'],
          		'server_token' => $serverToken,
          		'use_sandbox'  => $conf['useSandbox'], // optional, default false
          		'version'      => 'v1', // optional, default 'v1'
          		'locale'       => 'en_US', // optional, default 'en_US'
        	));

    		try{
    			
    			$sendRequest = $client->requestRide(array(
      				'product_id' => $request['product_id'],
      				'start_latitude' => (string)$request['start_lat'],
      				'start_longitude' => (string)$request['start_lon'],
      				'end_latitude' => (string)$request['end_lat'],
      				'end_longitude' => (string)$request['end_lon']
  				));
          var_dump($sendRequest);
          if(isset($sendRequest->status)){
            
            $em = $this->getContainer()->get('Doctrine')->getManager();
            $requestObject = $em->getRepository('UberScheduleBundle:Request')->find($request['rid']);

            if ($requestObject instanceof \UberScheduleBundle\Entity\Request){

              $requestObject->setStatus($sendRequest->status);
              $requestObject->setRequestId($sendRequest->request_id);
              $em->persist($requestObject);
              $em->flush();
            }
         
          }
    		
    		 }catch(GuzzleHttp\Ring\Exception\ConnectException $e){
    		 	$output->writeln($e->getMessage());
    		 }
    		   
    		  

    	} 
    	
    }

}
