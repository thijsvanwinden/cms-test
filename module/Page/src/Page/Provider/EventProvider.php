<?php

namespace Page\Provider;

use Zend\ServiceManager\ServiceLocatorAwareInterface as ServiceLocatorAware,
    Zend\EventManager\EventManagerInterface as EventCollection,
    Zend\EventManager\EventManager,
    Zend\EventManager\ListenerAggregateInterface as ListenerAggregate,
    Zend\EventManager\EventInterface as Event,
    Zend\EventManager\EventManagerAwareInterface as EventManagerAware,
    Zend\EventManager\EventsCapableInterface as EventsCapable,
    Zend\ServiceManager\ServiceLocatorInterface as ServiceLocator,
    Traversable;

/**
 * Description of EventProvider
 *
 * @author Fam. Van Winden
 */
abstract class EventProvider implements ServiceLocatorAware, EventManagerAware, EventsCapable {

    protected $events;
    protected $listeners = array();
    protected $event = '\Page\Model\ModelEvent';
    protected $locator;

    public function __construct($options = array()) {
        if (is_array($options) || $options instanceof Traversable) {
            $this->setOptions($options);
        }
    }

    public function setOptions($options) {
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    public function init() {
        $this->events()->trigger(__FUNCTION__, $this);
    }

    public function events() {
        if (!$this->events instanceof EventCollection) {
            $this->setEventManager(new EventManager(array('Provider', __CLASS__, get_class($this))));
            $this->attach($this->events);
            $this->init();
        }
        return $this->events;
    }

    public function setEventManager(EventCollection $events) {
        $this->events = $events;
        return $this;
    }

    public function getEvent($eventName, $params = array()) {
        if (is_string($this->event)) {
            $event = new $this->event();
        } elseif ($this->event instanceof Event) {
            $event = $this->event;
        }

        if (!$event instanceof Event) {
            throw new Exception\DomainException("Event is not an instanceof Zend\EventManager\Event");
        }
        $event->setName($eventName)
                ->setTarget($this)
                ->setParams($params);

        return $event;
    }

    public function getServiceLocator() {
        return $this->locator;
    }

    public function setServiceLocator(ServiceLocator $locator) {
        $this->locator = $locator;
        return $this;
    }

}
