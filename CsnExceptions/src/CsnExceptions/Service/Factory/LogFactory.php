<?php

/**
 * CsnExceptions - Coolcsn Zend Framework 2 Exception Handling Module
 * 
 * @link https://github.com/coolcsn/CsnExceptions for the canonical source repository
 * @copyright Copyright (c) 2005-2014 LightSoft 2005 Ltd. Bulgaria
 * @license https://github.com/coolcsn/CsnExceptions/blob/master/LICENSE BSDLicense
 * @author Stoyan Cheresharov <stoyan@coolcsn.com>
 * @author Nikola Vasilev <niko7vasilev@gmail.com>
 * @author Svetoslav Chonkov <svetoslav.chonkov@gmail.com>
 */

namespace CsnExceptions\Service\Factory;
 
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Log\Logger;
use Zend\Log\Writer\Null;
use CsnExceptions\Log\Writer\DoctrineDB;
 
class LogFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
       /* $filename = 'log_' . date('F:Y') . '.txt';*/
        $log = new Logger();

        /*if(!is_dir('./data/logs')){
            mkdir('./data/logs');
            chmod('./data/logs', 0777);
        }*/
        
        //$writer = new DoctrineDB();
        $writer = new Null();
        //$log->addWriter($writer);
        $log->addWriter($writer);
        return $log;
    }
}