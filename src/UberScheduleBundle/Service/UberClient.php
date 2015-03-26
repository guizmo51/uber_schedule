<?php

namespace UberScheduleBundle\Service;
use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
 
class UberClient extends GuzzleClient
{
    public function __construct(array $matpe = [], array $config = []){
        $resolver = new OptionsResolver();
        $this->configureOptionResolver($resolver);

        // validation des paramètres 
        $options = $resolver->resolve($matpe);
 
        // initialisation du client standard Guzzle
$array = array(
            				"defaults" => array(
            					"headers" => array(
            						"Authorization" => "Token ".$options["serverToken"].""
            						)
            				),
            				"base_url" => $options["baseurl"]
        );


        $client = new Client((array) $array);
 $locator = new FileLocator(__DIR__.'/../Resources/config');
        $jsonFiles = $locator->locate('webservices.json', null, false);
        $description = new Description([
            "name" => 'Uber',
            "description" => "Exemple d'API MaTpe avec Guzzle",
            "operations" => [
                "getProducts" => [
                    "httpMethod" => "GET",
                   "uri"=> "products",
                    "responseModel" => "jsonResponse",
                    "additionalParameters" => [
                       "location"=>"query"
                    ]
                ],
                "getProduct" => [
                    "httpMethod" => "GET",
                    "uri" => "products/{id}",
                    "responseModel" => "jsonResponse",
                    "parameters" => [
                        "id" => [
                            "required" => true,
                            "location" => "uri"
                        ]
                    ]
                ],
                "getEstimatesPrice" => [
                        "httpMethod" => "GET",
                        "uri" => "estimates/price",
                        "responseModel" => "jsonResponse",
                    "additionalParameters" => [
                       "location"=>"query"
                    ]

                ],
                  "getEstimatesTime" => [
                        "httpMethod" => "GET",
                        "uri" => "estimates/time",
                        "responseModel" => "jsonResponse",
                    "additionalParameters" => [
                       "location"=>"query"
                    ]

                ],
              
            ],
            // les models permettent de définir le traitement appliqué aux réponses de l'API
            // on spécifie ici que l'on veut un objet php à partir du json contenu dans la réponse
            "models" => [
                "jsonResponse" => [
                    "type" => "object",
                    "additionalProperties" => [
                        "location" => "json"
                    ]
                ]
            ]
        ]);
 
        parent::__construct($client, $description, (array)$array);
    }
     protected function configureOptionResolver(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setRequired([
                'baseurl',
                'serverToken',
                'idClient',
                'secretClient'
            ])
        ;
    }

}


