<?php

namespace UberScheduleBundle\Service;
use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
 
class UberAuthClient extends GuzzleClient
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
                                    "Content-Type" =>"application/x-www-form-urlencoded",
            						)
            				),
            				"base_url" => $options["baseurl"]
        );


        $client = new Client((array) $array);
        $description = new Description([
            "name" => 'UberAuth',
            "description" => "Authentification to Uber WS",
            "operations" => [
                "getAccessToken" => [
                    "httpMethod" => "POST",
                   "uri"=> "oauth/token",
                    "responseModel" => "jsonResponse",
                    "parameters" => [
                        "client_id" => [
                            "required" => true,
                            "location" => "json"
                        ],
                        "client_secret" => [
                            "required" => true,
                            "location" => "json"
                        ],
                         "code" => [
                            "required" => true,
                            "location" => "json"
                        ],
                         "redirect_uri" => [
                            "required" => true,
                            "location" => "json"
                        ],
                        "grant_type" => [
                            "required" => true,
                            "location" => "json"
                        ],
                    ]
                ]

                ],
            // les models permettent de définir le traitement appliqué aux réponses de l'API
            // on spécifie ici que l'on veut un objet php à partir du json contenu dans la réponse
            "models" => [
                "jsonResponse" => [
                    "type" => "object",
                    "additionalProperties" => [
                        "location" => "uri"
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


