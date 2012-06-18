<?php

namespace Page\Layout\Node;

use ArrayObject,
    Zend\Loader\Broker;

/**
 * Description of TreeNodeStack
 *
 * @author Fam. Van Winden
 */
class TreeNodeStack extends ArrayObject implements NodeStack {
    
    protected $broker;
    public function __construct()
    {
        $this->getBroker()->getClassLoader()->registerPlugins(array(            
            'action' => 'Page\Layout\Node\Type\ActionNode',
            'content' => 'Page\Layout\Node\Type\ContentNode'
        ));
    }
    
    public function addNode($nodeName, $node) {
        if (!$node instanceof Node && !is_array($node)) {
            throw new Exception\InvalidArgumentException(sprintf("Node must be an instanceof Node or be an array, %s given.", get_type($node)));
        }
        $this[$nodeName] = $node;
    }

    public function addNodes($nodes) {
        foreach ($nodes as $nodeName => $node) {
            $this->addNode($nodeName, $node);
        }
        return $this;
    }

    public function getNode($nodeName) {
        if (!isset($this[$nodeName])) {
            return false;
        }
        if ($this[$nodeName] instanceof Node) {
            return $this[$nodeName];
        } else {
            $this[$nodeName] = $this->nodeFromArray($nodeName, $this[$nodeName]);
        }
        return $this[$nodeName];
    }

    public function getNodes() {
        $nodes = array();
        foreach ($this as $nodeName => $node) {
            if (!$node instanceof Node) {
                $node = $this->getNode();
            }
            $nodes[$nodeName] = $node;
        }
        return $nodes;
    }

    protected function nodeFromArray($nodeName, $options) {
        if (!isset($options['type'])) {
            throw new Exception\InvalidArgumentException(sprintf("Node must have a type to lazy load it."));
        }
        if(!isset($options['options'])){
            $options['options'] = array();
        }
        
        return $this->getBroker()->load($options['type'], $options['options']);
    }

    public function hasNode($nodeName) {
        
    }

    public function removeNode($nodeName) {
        
    }

    /**
     * Get node broker instance
     *
     * @return Zend\Loader\Broker
     */
    public function getBroker() {
        if (!$this->broker) {
            $this->setBroker(new NodeBroker());
        }
        return $this->broker;
    }

    /**
     * Set node broker instance
     *
     * @param  string|Broker $broker Plugin broker to load nodes
     * @return Zend\Loader\Pluggable
     */
    public function setBroker($broker) {
        if (!$broker instanceof Broker) {
            throw new Exception\InvalidArgumentException('Broker must implement Zend\Loader\Broker');
        }
        $this->broker = $broker;
        if (method_exists($broker, 'setNodeStack')) {
            $this->broker->setNodeStack($this);
        }
        return $this;
    }

}
