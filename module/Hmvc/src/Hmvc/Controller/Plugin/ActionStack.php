<?php

namespace Hmvc\Controller\Plugin;

use Zend\Mvc\InjectApplicationEvent,
    Zend\Mvc\LocatorAware,
    Zend\Mvc\Exception,
    Zend\Mvc\MvcEvent,
    Zend\Mvc\Router\RouteStack,
    Zend\Stdlib\RequestDescription as Request,
    Zend\Stdlib\ResponseDescription as Response,
    Zend\Stdlib\Dispatchable,
    Zend\Mvc\Controller\Plugin\AbstractPlugin,  
    Hmvc\Dispatcher\TreeStackDispatcher,
    Hmvc\Dispatcher\DispatchStack,
    Hmvc\Dispatcher\DispatchStackAggregate,
    Hmvc\Exception\DomainException;

class ActionStack extends AbstractPlugin implements DispatchStack, DispatchStackAggregate {

    /**
     * The dispatch stack
     *
     * @var DispatchStack
     */
    protected $dispatchables;

    /**
     * Add dispatchable
     * 
     * @param Dispatchable The dispatchable object
     * 
     * @return ActionStack
     */
    public function addDispatchable($name, $dispatchable, $priority=null) {
        $this->getDispatchStack()->addDispatchable($name, $dispatchable, $priority);
        return $this;
    }

    /**
     * Add dispatchables
     * 
     * @param array $dispatchables An array of dispatchables
     * 
     * @return ActionStack
     */
    public function addDispatchables($dispatchables) {
        foreach ($dispatchables as $name => $dispatchable) {
            $this->addDispatchable($name, $dispatchable);
        }
        return $this;
    }

    /**
     * Remove a dispatchable
     * 
     * @param string $name The name of the dispatchable
     * @return ActionStack
     */
    public function removeDispatchable($name) {
        $this->getDispatchStack()->removeDispatchable($name);
        return $this;
    }

    /**
     * Get the dispatch stack from the aggregate
     * 
     * @return DispatchStack
     */
    public function getDispatchStack() {
        if ($this->dispatchables === null) {
            $this->dispatchables = new TreeStackDispatcher();
        }
        return $this->dispatchables;
    }

    /**
     * Set the dispatch stack
     * 
     * @return ActionStack
     */
    public function setDispatchStack(Dispatchable $dispatchStack) {
        if ($dispatchStack instanceof DispatchStackAggregate) {
            $this->dispatchables = $dispatchStack->getDispatchStack();
        } elseif ($dispatchStack instanceof DispatchStack) {
            $this->dispatchables = $dispatchStack;
        } else {
            throw new DomainException("Dispatch stack must be instanceof DispatchStackAggregate or DispatchStack");
        }
    }

    /**
     * Dispatches the result 
     *       
     * @return $result
     */
    public function dispatch(Request $request = null, $response = null) {
        $controller = $this->getController();
        if (!$controller instanceof InjectApplicationEvent) {
            throw new DomainException("Controller should implement InjectApplicationEvent");
        }
        
        $e = $controller->getEvent();
        
        $stack = $this->getDispatchStack();
        
        if ($stack instanceof InjectApplicationEvent) {
            $stack->setEvent($e);
        }
        if ($stack instanceof LocatorAware && $controller instanceof LocatorAware) {
            $stack->setLocator($controller->getLocator());
        }
        
        if($request === null){
            $request = $e->getRequest();
        }
        
        if($response === null){
            $response = $e->getResponse();
        }
        
        return $stack->dispatch($request, $response);
    }

}
