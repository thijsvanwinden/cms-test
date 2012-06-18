<?php

namespace Hmvc\Dispatcher;

use ArrayObject,
    Zend\Di\Locator,
    Zend\EventManager\EventCollection,
    Zend\EventManager\EventDescription as Event,
    Zend\EventManager\EventManager,
    Zend\Http\PhpEnvironment\Response as HttpResponse,
    Zend\Loader\Pluggable,
    Zend\Stdlib\Dispatchable,
    Zend\Stdlib\IsAssocArray,
    Zend\Stdlib\RequestDescription as Request,
    Zend\Stdlib\ResponseDescription as Response,
    Zend\Mvc\InjectApplicationEvent,
    Zend\Mvc\LocatorAware,
    Zend\Mvc\MvcEvent,
    Zend\Mvc\Router\RouteMatch,
    Zend\Stdlib\SplPriorityQueue,
    Traversable;

/**
 * Description of SimpleStackDispatcher
 *
 * @author Fam. Van Winden
 */
class SimpleStackDispatcher implements Dispatchable, LocatorAware, InjectApplicationEvent, DispatchStack, DispatchStackAggregate {

    //use \Zend\EventManager\ProvidesEvents;

    protected $event;
    protected $events;
    protected $locator;
    protected $dispatchables = array();

    /**
     * Constructs the dispatch stack
     * 
     */
    public function __construct($config = array()) {
        if (is_array($config) || $config instanceof Traversable) {
            $this->setOptions($config);
        }
    }

    public function setOptions(array $options) {
        if (isset($options['dispatchables']) && is_array($options['dispatchables'])) {
            $this->addDispatchables($options['dispatchables']);
        }

        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    /**
     * Add dispatchable
     * 
     * @param Dispatchable The dispatchable object
     * 
     * @return SimpleStackDispatcher
     */
    public function addDispatchable($name, $dispatchable, $priority=null) {
        if ($priority === null) {
            if (is_array($dispatchable) && isset($dispatchable['priority'])) {
                $priority = $dispatchable['priority'];
            }
        }

        $this->dispatchables[$name] = $dispatchable;

        //$this->dispatchables->insert($name, $dispatchable, $priority);

        return $this;
    }

    /**
     * Add dispatchables
     * 
     * @param array $dispatchables An array of dispatchables
     * 
     * @return SimpleStackDispatcher
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
     * @return SimpleStackDispatcher
     */
    public function removeDispatchable($name) {
        unset($this->dispatchable[$name]);
        //$this->dispatchables->remove($name);
        return $this;
    }

    /**
     * Get the dispatch stack from the aggregate
     * 
     * @return SimpleStackDispatcher
     */
    public function getDispatchStack() {
        return $this;
    }

    /**
     * Dispatch a request
     * 
     * @events dispatch.pre, dispatch.post
     * @param  Request $request 
     * @param  null|Response $response 
     * @return Response|mixed
     */
    public function dispatch(Request $request, Response $response = null) {
        if (!$response) {
            $response = new HttpResponse();
        }

        $e = $this->getEvent();
        $e->setRequest($request)
                ->setResponse($response)
                ->setTarget($this);

        $result = $this->events()->trigger('dispatch', $e, function($test) {
                    return ($test instanceof Response);
                });

        if ($result->stopped()) {
            return $result->last();
        }
        return $e->getResult();
    }

    /**
     * Execute the request
     * 
     * @param  MvcEvent $e 
     * @return mixed
     */
    public function execute(MvcEvent $e) {
        $routeMatch = $e->getRouteMatch();
        if (!$routeMatch) {
            /**
             * @todo Determine requirements for when route match is missing.
             *       Potentially allow pulling directly from request metadata?
             */
            throw new \DomainException('Missing route matches; unsure how to retrieve action');
        }


        $this->paramsCache = $routeMatch;
        $params = clone $this->paramsCache;

        $request = $e->getRequest();
        $response = $e->getResponse();

        $stack = $this->dispatchables;
        $actionResponses = array();
        foreach ($stack as $name => $dispatchable) {

            if (is_array($dispatchable)) {
                $dispatchable = $this->dispatchableFromArray($name, $dispatchable, $params);
            }

            $actionResponse = $dispatchable->dispatch($request, $response);

            if ($actionResponse instanceof Response) {
                $actionResponses = $actionResponse;
                break;
            }

            if (!is_object($actionResponse)) {
                if (IsAssocArray::test($actionResponse)) {
                    $actionResponses[$name] = new ArrayObject($actionResponse, ArrayObject::ARRAY_AS_PROPS);
                } else {
                    $actionResponses[$name] = $actionResponse;
                }
            } else {
                $actionResponses[$name] = $actionResponse;
            }
        }

        $e->setRouteMatch($this->paramsCache)
                ->setResult($actionResponses);
        return $actionResponses;
    }

    /**
     * Set the event manager instance used by this context
     * 
     * @param  EventCollection $events 
     * @return AppContext
     */
    public function setEventManager(EventCollection $events) {
        $this->events = $events;
        return $this;
    }

    /**
     * Retrieve the event manager
     *
     * Lazy-loads an EventManager instance if none registered.
     * 
     * @return EventCollection
     */
    public function events() {
        if (!$this->events instanceof EventCollection) {
            $this->setEventManager(new EventManager(array(
                        'Zend\Stdlib\Dispatchable',
                        __CLASS__,
                        get_called_class()
                    )));
            $this->attachDefaultListeners();
        }
        return $this->events;
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

    /**
     * Register the default events for this controller
     * 
     * @return void
     */
    protected function attachDefaultListeners() {
        $events = $this->events();
        $events->attach('dispatch', array($this, 'execute'));
    }

    /**
     * Create a dispatchable from an array
     * 
     * @return Dispatchable
     */
    protected function dispatchableFromArray($name, $options, RouteMatch $params) {

        $event = $this->getEvent();
        $dispatchable = $this->getLocator()->get($options['params']['controller']);

        if (isset($options['params'])) {
            foreach ($options['params'] as $key => $param) {
                $params->setParam($key, $param);
            }
        }
        $event->setRouteMatch($params);

        if ($dispatchable instanceof InjectApplicationEvent) {
            $dispatchable->setEvent($event);
        }
        if ($dispatchable instanceof LocatorAware) {
            $dispatchable->setLocator($this->getLocator());
        }

        return $dispatchable;
    }

}
