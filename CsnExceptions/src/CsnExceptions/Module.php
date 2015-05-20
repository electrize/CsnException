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

namespace CsnExceptions;

use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventInterface;
use Zend\View\Model\ViewModel;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        //$start = microtime(true);
        // Turn off all error reporting
        error_reporting(0);
        //ini_set("display_errors", true);
        set_error_handler(array($this, 'handlePHPErrors'));

        $eventManager   = $e->getApplication()->getEventManager();

        $eventManager->attach(array(MvcEvent::EVENT_DISPATCH_ERROR), array($this, 'onDisplayException'), -1000);

        $serviceManager = $e->getApplication()->getServiceManager();

        register_shutdown_function(array($this, 'handlePHPFatalErrors'), $serviceManager);

        //$exceptionstrategy = $serviceManager->get('ViewManager')->getExceptionStrategy();
        //$exceptionstrategy->setExceptionTemplate('csn-exceptions/error/custom');
        $end = microtime(true);
        //print "<br><br><br><br>CsnExceptions generated in ".round(($end - $start), 3)." seconds";
    }

    public function onDisplayException(EventInterface $event)
    {
        if (!$event->isError()) {
                return;
        }
        $serviceManager = $event->getApplication()->getServiceManager();

        $exception = $event->getParam('exception', false);

        if ($exception) {

            //
            //  Error Logging
            //  
            /*do {
                $serviceManager->get('Logger')->crit(
                    sprintf(
                        "%s:%d %s (%d) [%s]\n",
                        $exception->getFile(),
                        $exception->getLine(),
                        $exception->getMessage(),
                        $exception->getCode(),
                        get_class($exception)
                    )
                );
            }
            while($ex = $exception->getPrevious());
            */
           
            //
            //  Prepare Error View
            //  
            $display_exceptions = $serviceManager->get('Configuration')['view_manager']['display_exceptions'];
            $userErrorMessage = $serviceManager->get('Config')['exception_options']['userErrorMessage'];

            $exceptionType = $this->errorCodeToString($exception->getCode());

            $childModel = new ViewModel();
            $childModel->setTemplate('csn-exceptions/error/custom');
            $childModel->setVariables(array(
                'errorMessage' => $userErrorMessage,
                'display_exceptions' => $display_exceptions,
                'exception' => $exception,
                'type' => $exceptionType,
            ));

            $baseModel = new ViewModel();
            $baseModel->setTemplate('layout/layout');
            $baseModel->addChild($childModel);
            $baseModel->setTerminal(true);
            
            $response = $event->getResponse();
            $response->setStatusCode(302);
            
            $event->setViewModel($baseModel);
            $event->setResponse($response);
            $event->stopPropagation(true);
        }      
    }

    public function handlePHPErrors($codeNum, $message, $file, $line)
    {
        // convert php errors into exceptions
        throw new \ErrorException($message, $codeNum, $severity = 0, $file, $line);
        return false;
    }

    public function handlePHPFatalErrors($serviceManager)
    {
        define('E_FATAL',  E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR |
        E_COMPILE_ERROR | E_RECOVERABLE_ERROR);

        $error = error_get_last();

        if ($error && ($error['type'] & E_FATAL)) {

            // check if there's an active output buffer and clean it
            while( ob_get_level() > 0 ) {
                ob_end_clean();
            }
            
            //
            //  Error Details
            //  
            $codeNum = $error['type'];
            $message = $error['message'];
                    
            $file = $error['file'];
            $line = $error['line'];

            //
            //  Error Logging
            //  
            //$logger->ERR($e['message'] . " in " . $e['file'] . ' line ' . $e['line']);
            //$logger->__destruct();

            
            //
            //  Prepare Error View
            //  
            $userErrorMessage = $serviceManager->get('Config')['exception_options']['userErrorMessage'];

            $config = $serviceManager->get('Config');

            $fatal_error_template_path = NULL;
            if(array_key_exists('fatal_error_template',$config['exception_options']['templates'])) {
                $fatal_error_template_path = $config['exception_options']['templates']['fatal_error_template'];
            }

            $exception_template_path = NULL;
            if(array_key_exists('exception_template',$config['exception_options']['templates'])) {
                $exception_template_path = $config['exception_options']['templates']['exception_template'];
            }
            
            if (!empty($fatal_error_template_path) && file_exists($fatal_error_template_path)) {
                
                ob_start();
                $display_exceptions = $serviceManager->get('Config')['view_manager']['display_exceptions'];
                $exception = new \ErrorException($message, $codeNum, $severity = 0, $file, $line);
                $type = $this->errorCodeToString($exception->getCode());

                if(!empty($exception_template_path) && file_exists($exception_template_path)) {
                    include($exception_template_path);
                }
                $content = ob_get_clean();

               ob_start(function($buffer) use ($content) {
                    $buffer = str_replace("###_EXCEPTION_###", $content, $buffer);
                    return $buffer;
                });
                include($fatal_error_template_path);
                ob_end_flush();

            } else {
                // In case no template found
                echo '<br/><br/><br/><center>';  
                echo "<h1>Something went wrong! Please, try again later.</h1>";
                echo "<h2>We will do our best to fix the problem as soon as posible.<h2>";
                echo "<h3>Sorry for the inconvenience!</h3>";
                echo "<hr/>";
                echo "<h4>Try to go to the home page:</h4>";
                echo '<h4><a href="/">HOME</a></h4>';
                echo '<h3>OR</h3>';
                echo "<h3>use back button of the browser to continue.<h3>";
                echo '</center>';
            }
            die(1);
        }

    }

    private function errorCodeToString($type) 
    { 
        switch($type) 
        { 
            case 0: // 0 // 
                return 'User-defined exception'; 
            case E_ERROR: // 1 // 
                return 'Fatal run-time error (E_ERROR)'; 
            case E_WARNING: // 2 // 
                return 'Run-time warning (E_WARNING)'; 
            case E_PARSE: // 4 // 
                return 'Fatal compile-time error generated by the parser (E_PARSE)'; 
            case E_NOTICE: // 8 // 
                return 'Run-time notice (E_NOTICE)'; 
            case E_CORE_ERROR: // 16 // 
                return 'Fatal error generated by the core of PHP (E_CORE_ERROR)'; 
            case E_CORE_WARNING: // 32 // 
                return 'Warning generated by the core of PHP (E_CORE_WARNING)'; 
            case E_COMPILE_ERROR: // 64 // 
                return 'Fatal compile-time error generated by the Zend Scripting Engine (E_COMPILE_ERROR)'; 
            case E_COMPILE_WARNING: // 128 // 
                return 'Compile-time warning generated by the Zend Scripting Engine (E_COMPILE_WARNING)'; 
            case E_USER_ERROR: // 256 // 
                return 'User-generated error message (E_USER_ERROR)'; 
            case E_USER_WARNING: // 512 // 
                return 'User-generated warning message (E_USER_WARNING)'; 
            case E_USER_NOTICE: // 1024 // 
                return 'User-generated notice message (E_USER_NOTICE)'; 
            case E_STRICT: // 2048 // 
                return 'E_STRICT'; 
            case E_RECOVERABLE_ERROR: // 4096 // 
                return 'Catchable fatal error (E_RECOVERABLE_ERROR)'; 
            case E_DEPRECATED: // 8192 // 
                return 'Run-time notice (E_DEPRECATED)'; 
            case E_USER_DEPRECATED: // 16384 // 
                return 'User-generated warning message (E_USER_DEPRECATED)'; 
            default:
                return 'Undefined exception type';
        } 
        return ""; 
    } 

    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/../../src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
}