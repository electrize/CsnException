<?php

namespace CsnExceptions;

return array(
    'exception_options' => array(
        'userErrorMessage' => 'Something went wrong! Please, try again later.',
        'templates' => array(
            'fatal_error_template' => __DIR__ . '/../view/csn-exceptions/error/fatal.phtml',
            'exception_template' => __DIR__ . '/../view/csn-exceptions/error/exception.phtml',
        ),
    ),
    'service_manager' => array (
        'factories' => array(
            'Logger' => 'CsnExceptions\Service\Factory\LogFactory',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'CsnExceptions\Controller\Index' => 'CsnExceptions\Controller\IndexController'
        )
    ),
    'router' => array(
        'routes' => array(
            'admin-logs' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/admin-logs/errors[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                       '__NAMESPACE__' => 'CsnExceptions\Controller',
                        'controller' => 'Index',
                        'action' => 'index'
                    ),
                ),
                'may_terminate' => true,
            )
        )
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'template_map' => array(
            'csn-exceptions/error/custom' => __DIR__ . '/../view/csn-exceptions/error/custom.phtml',
            'csn-exceptions/error/exception_template' => __DIR__ . '/../view/csn-exceptions/error/exception.phtml'
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view'
        )
    ),
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/' . __NAMESPACE__ . '/Entity',
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver',
                )
            )
        )
    ),
);
