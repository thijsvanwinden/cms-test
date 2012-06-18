<?php

namespace Page\Provider;

use Zend\EventManager\EventCollection,
    Zend\EventManager\ListenerAggregate, 
    Page\Model\Model;

/**
 * Description of PageProvider
 *
 * @author Fam. Van Winden
 */
class ContentProvider extends EventProvider implements ListenerAggregate {

    /**
     *
     * @var Model
     */
    protected $model;

    public function attach(EventCollection $events) {
        $model = $this->getModel();
        if ($model instanceof ListenerAggregate) {
            $model->attach($events);
        }
    }

    public function detach(EventCollection $events) {
        $model = $this->getModel();
        if ($model instanceof ListenerAggregate) {
            $model->detach($events);
        }
    }

    public function getContentById($contentId) {
        $parameters = array(
            'content_id' => $contentId
        );
        $event = $this->getEvent(__FUNCTION__, $parameters);

        $result = $this->events()->trigger($event, function($res) {
                    $res instanceof Carrier;
                });
                
        if ($result->stopped()) {
            return $result->last();
        } else {
            return $event->getResult();
        }
    }

    public function save($content) {
        $parameters = array(
            'content' => $content
        );
        $event = $this->getEvent(__FUNCTION__, $parameters);

        $result = $this->events()->trigger($event, function($res) {
                    $res instanceof Carrier;
                });

        if ($result->stopped()) {
            return $result->last();
        } else {
            return $event->getResult();
        }
    }

    public function setModel(Model $model) {
        $this->model = $model;
        return $this;
    }

    public function getModel() {
        return $this->model;
    }
}
