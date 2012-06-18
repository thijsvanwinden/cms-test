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
    Zend\View\Renderer,
    Hmvc\Exception\InvalidArgumentException,
    Traversable;

class ViewScriptSwitcher {

    protected $staticListeners = array();
    protected $view;
    protected $contents = array();
    protected $viewScripts = array();
    
    public function registerStaticListeners(StaticEventCollection $events) {
        $ident = 'Hmvc\Dispatcher\SimpleStackDispatcher';
        $handler = $events->attach($ident, 'dispatch', array($this, 'switchViewScript'), 25);
        $this->staticListeners[] = array($ident, $handler);
    }

    public function detachStaticListeners(StaticEventCollection $events) {
        foreach ($this->staticListeners as $i => $info) {
            list($id, $handler) = $info;
            $events->detach($id, $handler);
            unset($this->staticListeners[$i]);
        }
    }

    public function switchViewScript(MvcEvent $e) {
        $name = $e->getParam('name');
        
        if(!$name){
            $name = 'default';
            $e->setParam('name', $name);
        }
        
        if (isset($this->viewScripts[$name])) {
            $e->setParam('viewScript', $this->viewScripts[$name]);
        }
    }

    public function addViewScripts($groupings) {
        foreach ($groupings as $name => $grouping) {
            $this->addGrouping($name, $grouping);
        }
        return $this;
    }

    public function addViewScript($name, $grouping) {
        $this->viewScripts[$name] = $grouping;
    }
    
    public function getViewScript()
    {
        return $this->viewScripts;
    }
    
    public function setViewScriptStack($viewScriptStack)
    {
        if(!is_array($viewScriptStack) && !$viewScriptStack instanceof Traversable){
            throw new InvalidArgumentException("ViewScriptStack must be an array of be traversable.");            
        }
        
        $this->viewScripts = $viewScriptStack;
    }

}
