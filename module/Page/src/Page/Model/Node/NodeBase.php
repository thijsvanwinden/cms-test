<?php

namespace Page\Model\Node;

use Hmvc\Dispatcher\DispatchStackAggregate,
    Hmvc\View\ViewScriptStackAggregate,
    Page\Carrier,
    ArrayObject,
    Zend\Mvc\Router\RouteMatch;

/**
 * Description of ActionNode
 *
 * @author Fam. Van Winden
 */
class NodeBase extends ArrayObject implements Node, Carrier, DispatchStackAggregate, ViewScriptStackAggregate {

    public function __construct($array = array()) {
        if (!is_array($array)) {
            $array = array();
        }
        parent::__construct($array, self::ARRAY_AS_PROPS);
    
        
    }
    
    public function getNodeId()
    {
        return $this->node_id;
    }

    public function getDispatchStack() {
        return array(
            'params' => $this->getParams()
        );
    }
    
    public function getViewScriptStack()
    {
        return array();
    }

    public function getName() {
        return $this->name;
    }

    public function getParams() {
        return $this->params;
    }
    
    public function getEditRoute(){
        $route = new RouteMatch(array(
            'controller'=> 'content',
            'action' => 'save',
            'name' => $this->getName()
        ));
        $route->setMatchedRouteName('content/save');
        return $route;
    }

}

