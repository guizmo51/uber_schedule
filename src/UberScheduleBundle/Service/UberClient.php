<?php

namespace UberScheduleBundle\Service;
use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
 
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
 
        // définition des requètes supportées par notre service
        $description = new Description([
            "name" => 'MaTpe',
            "description" => "Exemple d'API MaTpe avec Guzzle",
            // list des opérations supportées
            "operations" => [
                // pour commencer, une simple récupération de la liste des clients
                "getProducts" => [
                    "httpMethod" => "GET",
                    // l'uri est ajoutée à notre base_url définie par défaut
                    "uri"=> "products",
                    // la réponse attendue sera traitée avec le model jsonResponse, 
                    // déclaré plus bas dans "models"
                    "responseModel" => "jsonResponse",
                    // par défaut tout paramètre additionnel passé à cette requète
                    // sera envoyé dans le query_string de l'url appelée
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


