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
class NodeProvider extends EventProvider implements ListenerAggregate {

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

    public function getNodesByPageId($pageId) {
        $parameters = array(
            'pageId' => $pageId
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

    public function save($node) {
        $parameters = array(
            'node' => $node
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

    /** protected $methods = array(
      'getPageById' => array(
      'parameters' => array(
      'pageid',
      'options' => array()
      ),
      ),
      'save' => array(
      'parameters' => array(
      'page',
      'options' => array()
      )
      ),
      'delete' => array(
      'parameters' => array(
      'pageid',
      'options' => array()
      )
      )
      ); */
    /**   public function __call($method, $arguments)
      {

      var_dump($arguments);
      if(!in_array($method, $this->methods)){
      throw new Exception\BadMethodCallException(sprintf("Method %s does not exists"), $method);
      }

      $methodOptions = $this->methods[$method];
      if(isset($methodOptions['parameters']) && is_array($methodOptions['parameters'])){
      foreach($methodOptions['parameters'] as $key => $value){
      if(is_numeric($key)){
      $parameterName = $value;
      $default = null;
      } else {
      $parameterName = $key;
      $default = $value;
      }

      if(current($arguments) == )
      }
      }


      } */
}
