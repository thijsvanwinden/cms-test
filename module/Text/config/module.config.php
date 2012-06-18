<?php

return array(
    'controller' => array(
        'classes' => array(
            'text' => 'Text\Controller\TextController',
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'text/get' => __DIR__ . '/../view/text/get.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'di' => array(
        'instance' => array(
            'alias' => array(
                //controllers
                'textProvider' => 'Text\Provider\textProvider',
            ),
            'Text\Model\TextModel' => array(
                'parameters' => array(
                    'dbAdapter' => 'Zend\Db\Adapter\PdoMysql',
                )
            ),
            'Text\Provider\textProvider' => array(
                'parameters' => array(
                    'model' => 'Page\Model\ContentModel'
                )
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'text' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/text',
                    'defaults' => array(
                        'controller' => 'text',
                        'action' => 'index',
                    )
                ),
                'child_routes' => array(
                    'get' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/get/:nodeid',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'nodeid' => '[0-9]*',
                            ),
                            'defaults' => array(
                                'action' => 'get',
                            )
                        )
                    ),
                    'save' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/save/:name',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'name' => '[a-zA-Z]*',
                            ),
                            'defaults' => array(
                                'action' => 'save',
                            )
                        )
                    ),
                    'new' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/new/:name',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'name' => '[a-zA-Z]*',
                            ),
                            'defaults' => array(
                                'action' => 'new',
                            )
                        )
                    ),
                    'delete' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/delete/:name',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'name' => '[a-zA-Z]*',
                            ),
                            'defaults' => array(
                                'action' => 'delete',
                            )
                        )
                    )
                )
            )
        )
    )
);
