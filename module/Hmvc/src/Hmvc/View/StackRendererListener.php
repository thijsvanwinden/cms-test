<?php

namespace Hmvc\View;

use ArrayAccess,
    Zend\Di\Locator,
    Zend\EventManager\EventCollection,
    Zend\EventManager\ListenerAggregate,
    Zend\EventManager\StaticEventCollection,
    Zend\Http\PhpEnvironment\Response,
    Zend\Mvc\Application,
    Zend\Mvc\MvcEvent,
    Zend\View\Renderer;

class StackRendererListener {

    protected $staticListeners = array();
    protected $view;
    protected $contents = array();

    public function __construct(Renderer $renderer) {
        $this->view = $renderer;
    }

    public function registerStaticListeners(StaticEventCollection $events) {
        $ident = 'Hmvc\Dispatcher\SimpleStackDispatcher';
        $handler = $events->attach($ident, 'dispatch', array($this, 'renderView'), -50);
        $this->staticListeners[] = array($ident, $handler);
        $ident = 'Zend\Mvc\Controller\ActionController';
        $handler = $events->attach($ident, 'dispatch', array($this, 'gatherContent'), -51);
        $this->staticListeners[] = array($ident, $handler);
    }

    public function detachStaticListeners(StaticEventCollection $events) {
        foreach ($this->staticListeners as $i => $info) {
            list($id, $handler) = $info;
            $events->detach($id, $handler);
            unset($this->staticListeners[$i]);
        }
    }

    public function gatherContent(MvcEvent $e) {
        $content = $e->getParam('content');
        
        $name = array_pop(explode('/', $e->getParam('name')));
        
        if(empty($name)){
            $name = 'content';            
        }
        
        $this->view->placeholder($name)->set($content);
        
        
        $resultData = array(
            'content' => $content
        );
        
        $result = $e->getResult();        
        if (is_array($result)) {
            $result = array_merge($result, $resultData);
        } else {
            $result = $resultData;
        }
        $e->setResult($result);
        
        return $result;
    }

    public function renderView(MvcEvent $e) {
        $response = $e->getResponse();
        if (!$response->isSuccess()) {
            return;
        }
        
        $routeMatch = $e->getRouteMatch();
        $grouping = $e->getParam('viewScript', 'default');
        $script = 'groupings' . '/' . $grouping . '.phtml';

        $vars = $e->getResult();

        if (is_scalar($vars)) {
            $vars = array('content' => $vars);
        } elseif (is_object($vars) && !$vars instanceof ArrayAccess) {
            $vars = (array) $vars;
        }

        $content = $this->view->render($script, $vars);
        $e->setParam('content', $content);
        return $content;
    }

}

