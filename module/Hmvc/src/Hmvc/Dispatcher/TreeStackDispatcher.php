<?php

namespace Hmvc\Dispatcher;

use ArrayObject,
    Zend\Di\Locator,
    Zend\EventManager\EventCollection,
    Zend\EventManager\EventDescription as Event,
    Zend\EventManager\EventManager,
    Zend\Http\PhpEnvironment\Response as HttpResponse,
    Zend\Stdlib\IsAssocArray,
    Zend\Stdlib\RequestDescription as Request,
    Zend\Stdlib\ResponseDescription as Response,
    Zend\Mvc\Router\RouteMatch,
    Zend\Mvc\MvcEvent;

/**
 * Description of SimpleStackDispatcher
 *
 * @author Fam. Van Winden
 */
class TreeStackDispatcher extends SimpleStackDispatcher {

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

        $oldName = $e->getParam('name');

        $stack = $this->dispatchables;
        $actionResponses = array();
        foreach ($stack as $name => $dispatchable) {

            if (is_array($dispatchable)) {
                $dispatchable = $this->dispatchableFromArray($name, $dispatchable, $params);
            }

            if ($oldName) {
                $fullName = $oldName . '/' . $name;
            } else {
                $fullname = $name;
            }
            $e->setParam("name", $fullName);
            $dispatchable->setEvent($e);

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
     * Create a dispatchable from an array
     * 
     * @return Dispatchable
     */
    protected function dispatchableFromArray($name, $options, RouteMatch $params) {

        return parent::dispatchableFromArray($name, $options, $params);
        $event = $this->getEvent();

        $dispatchable = $this->getLocator()->get($options['params']['controller']);

        if (isset($options['params'])) {
            foreach ($options['params'] as $key => $param) {
                $params->setParam($key, $param);
            }
        }
        $event->setRouteMatch($params);

        return $dispatchable;
    }

}
