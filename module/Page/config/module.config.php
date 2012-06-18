<?php

return array(
    'controller' => array(
        'classes' => array(
            'node' => 'Page\Controller\NodeController',
            'page' => 'Page\Controller\PageController',
            'content' => 'Page\Controller\ContentController',
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'text/get' => __DIR__ . '/../view/text/get.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'helper_map' => array(
            'nodeeditorbar' => 'Page\View\Helper\NodeEditorBar',
            'formlayoutselect' => 'Page\View\Helper\FormLayoutSelect'
        )
    ),
    'di' => array(
        'instance' => array(
            'alias' => array(
                //controllers
                'layoutStack' => 'Page\Layout\LayoutStack',
                'layoutSelectForm' => 'Page\Form\LayoutSelectForm',
                //providers
                'pageProvider' => 'Page\Provider\PageProvider',
                'nodeProvider' => 'Page\Provider\NodeProvider',
                'contentProvider' => 'Page\Provider\ContentProvider',
                //entities
                'pageEntity' => 'Page\Model\Page\PageBase',
                'nodeEntity' => 'Page\Model\Node\NodeBase',
                'editorListener' => 'Page\View\EditorListener',
                'pageStorage' => 'Page\Storage\SessionStorage',
                'db' => 'Zend\Db\Adapter\PdoMysql',
            ),
            'Page\View\EditorListener' => array(
                'parameters' => array(
                    'view' => 'view'
                )
            ),
            'Page\Storage\SessionStorage' => array(
                'methods' => array(
                    '__construct' => array(
                        'sessionManager' => 'sessionManager'
                    )
                ),
                'parameters' => array(
                    'pageProvider' => 'pageProvider'
                )
            ),
            'Zend\Db\Adapter\PdoMysql' => array(
                'parameters' => array(
                    'config' => array(
                        'host' => 'localhost',
                        'username' => 'root',
                        'password' => '',
                        'dbname' => 'cms',
                    ),
                ),
            ),
            'Page\Provider\NodeProvider' => array(
                'parameters' => array(
                    'model' => 'Page\Model\NodeModel'
                )
            ),
            'Page\Model\NodeModel' => array(
                'parameters' => array(
                    'dbAdapter' => 'db'
                )
            ),
            'Page\Model\PageModel' => array(
                'parameters' => array(
                    'dbAdapter' => 'Zend\Db\Adapter\PdoMysql',
                )
            ),
            'Page\Provider\PageProvider' => array(
                'parameters' => array(
                    'model' => 'Page\Model\PageModel'
                )
            ),
            'Page\Model\ContentModel' => array(
                'parameters' => array(
                    'dbAdapter' => 'Zend\Db\Adapter\PdoMysql',
                )
            ),
            'Page\Provider\ContentProvider' => array(
                'parameters' => array(
                    'model' => 'Page\Model\ContentModel'
                )
            ),
            'Zend\Navigation\Navigation' => array(
                'parameters' => array(
                    'pages' => array(
                        'page' => array(
                            'type' => 'mvc',
                            'route' => 'page',
                            'label' => 'Pages',
                            'order' => 1,
                        )
                    )
                )
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'page' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/page',
                    'defaults' => array(
                        'controller' => 'page',
                        'action' => 'index',
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'get' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/get/:pageid',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'pageid' => '[0-9]*',
                            ),
                            'defaults' => array(
                                'action' => 'get',
                            )
                        )
                    ),
                    'save' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/save/:pageid',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'pageid' => '[0-9]*',
                            ),
                            'defaults' => array(
                                'action' => 'save',
                            )
                        )
                    )
                )
            ),
            'node' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/node',
                    'defaults' => array(
                        'controller' => 'node',
                        'action' => 'index',
                    )
                ),
                'may_terminate' => true,
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
                    )
                )
            ),
            'content' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/content',
                    'defaults' => array(
                        'controller' => 'content',
                        'action' => 'index',
                    )
                ),
                'may_terminate' => true,
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
