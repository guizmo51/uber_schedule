<?php

namespace UberScheduleBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class UberScheduleExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $locator = new FileLocator(__DIR__.'/../Resources/config');
        $jsonFiles = $locator->locate('webservices.json', null, false);
       
        $messages = json_decode(trim(file_get_contents($jsonFiles[0])), true);
  var_dump(json_last_error());
    }

     /**
      * Translates JSON_ERROR_* constant into meaningful message
      *
      * @param  integer $errorCode Error code returned by json_last_error() call
      * @return string  Message string
      */
     private function getJSONErrorMessage($errorCode)
     {
         $errorMsg = null;
         switch ($errorCode) {
             case JSON_ERROR_DEPTH:
                 $errorMsg = 'Maximum stack depth exceeded';
                 break;
             case JSON_ERROR_STATE_MISMATCH:
                 $errorMsg = 'Underflow or the modes mismatch';
                 break;
             case JSON_ERROR_CTRL_CHAR:
                 $errorMsg = 'Unexpected control character found';
                 break;
             case JSON_ERROR_SYNTAX:
                 $errorMsg = 'Syntax error, malformed JSON';
                 break;
             case JSON_ERROR_UTF8:
                 $errorMsg = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                 break;
             default:
                 $errorMsg = 'Unknown error';
             break;
         }
 
         return $errorMsg;
     }
}
