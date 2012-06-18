<?php

namespace Page\Layout\Node\Type;

use Page\Layout\Node\Node,
    Page\Layout\Node\Editable,
    Zend\Loader\LocatorAware,
    Zend\Di\Locator,
    Zend\Mvc\InjectApplicationEvent,
    Zend\EventManager\EventDescription as Event,
    Zend\Mvc\MvcEvent,
    Zend\Stdlib\Dispatchable;

/**
 * Description of ActionNode
 *
 * @author Fam. Van Winden
 */
class ActionNode implements Node, Dispatchable, Editable, LocatorAware, InjectApplicationEvent {
    
    protected $params = array();
    
    public static function factory($options) {
        $node = new static();
        if(isset($options['params'])){
            $node->params = array_merge($node->params, $options['params']);
        }                
        return $node;
    }
    
    public function getEditableRoute(){
        
    }
        /**
     * Set an event to use during dispatch
     *
     * By default, will re-cast to MvcEvent if another event type is provided.
     * 
     * @param  Event $e 
     * @return void
     */
    public function setEvent(Event $e) {
        if ($e instanceof Event && !$e instanceof MvcEvent) {
            $eventParams = $e->getParams();
            $e = new MvcEvent();
            $e->setParams($eventParams);
            unset($eventParams);
        }
        $this->event = $e;
    }

    /**
     * Get the attached event
     *
     * Will create a new MvcEvent if none provided.
     * 
     * @return Event
     */
    public function getEvent() {
        if (!$this->event) {
            $this->setEvent(new MvcEvent());
        }
        return $this->event;
    }

    /**
     * Set locator instance
     * 
     * @param  Locator $locator 
     * @return void
     */
    public function setLocator(Locator $locator) {
        $this->locator = $locator;
    }

    /**
     * Retrieve locator instance
     * 
     * @return Locator
     */
    public function getLocator() {
        return $this->locator;
    }
    
    public function dispatch(\Zend\Stdlib\RequestDescription $request, \Zend\Stdlib\ResponseDescription $response = null) {    
        $event   = $this->getEvent();
        $locator = $this->getLocator();
        
        $params = $this->params;
        
        if(!isset($params['controller'])){
            throw new Exception\InvalidArgumentException("No controller found in the parameters of a action node");            
        }
        $name = $params['controller'];

        $controller = $locator->get($name);
        if (!$controller instanceof Dispatchable) {
            throw new Exception\DomainException('Can only forward to Dispatchable classes; class of type ' . get_class($controller) . ' received');
        }
        if ($controller instanceof InjectApplicationEvent) {
            $controller->setEvent($event);
        }
        if ($controller instanceof LocatorAware) {
            $controller->setLocator($locator);
        }

        // Allow passing parameters to seed the RouteMatch with
        $cachedMatches = false;
        if ($params) {
            $matches       = new RouteMatch($params);
            $cachedMatches = $event->getRouteMatch();
            $event->setRouteMatch($matches);
        }

        $return = $controller->dispatch($event->getRequest(), $event->getResponse());

        if ($cachedMatches) {
            $event->setRouteMatch($cachedMatches);
        }

        return $return;   
    } 
    
}
