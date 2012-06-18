<?php

namespace Page\Model\Page;

use Page\Model\Node\NodeStackAggregate,
    Page\Model\Node\Node,
    Page\Carrier,
    Hmvc\Dispatcher\DispatchStackAggregate,
    Hmvc\Dispatcher\TreeStackDispatcher,
    Hmvc\View\ViewScriptStackAggregate,
    Zend\Di\Locator,
    Zend\Loader\LocatorAware,
    ArrayObject,
    Page\Model\Node\SplList;

/**
 * Description of PageBase
 *
 * @author Fam. Van Winden
 */
class PageBase extends ArrayObject implements Page, Carrier, NodeStackAggregate, DispatchStackAggregate, ViewScriptStackAggregate, Editable, \Serializable {

    protected $locator;

    public function __construct($array = array()) {
        if (!is_array($array)) {
            $array = array();
        }
        parent::__construct($array, self::ARRAY_AS_PROPS);
    }

    public function getPageId() {
        if (isset($this->pageId)) {
            return $this->pageId;
        } else {
            return $this->page_id;
        }
    }

    public function getDispatchStack() {
        $nodes = $this->getNodeStack();

        $dispatchStack = array();
        foreach ($nodes as $node) {
            $dispatchStack[$node->getName()] = $node->getDispatchStack();
        }

        return new TreeStackDispatcher(array(
                    'dispatchables' => $dispatchStack)
        );
    }

    public function getViewScriptStack() {

        $nodes = $this->getNodeStack();
        $viewScriptStack = array();

        $baseName = 'default';
        $viewScriptStack[$baseName] = $this->getViewScript();
        foreach ($nodes as $node) {
            $viewScripts = $node->getViewScriptStack();
            foreach ($viewScripts as $name => $viewScript)
                $viewScriptStack[$baseName . '/' . $name] = $viewScript;
        }

        return $viewScriptStack;
    }

    public function getEditableStack() {
        return $this;
        
        $nodes = $this->getNodeStack();

        $dispatchStack = array();        
        foreach ($nodes as $node) {
            $dispatchStack[$node->getName()] = array(
                'params' => array(
                    'controller' => 'node',
                    'action' => 'save',
                    'nodeid' => $node->getNodeId()
                )
            );
        }

        return new TreeStackDispatcher(array(
                    'dispatchables' => $dispatchStack)
        );
        
    }

    public function getViewScript() {
        return 'default';
    }

    public function getNodeStack() {
        $nodes = $this->getParam('nodes');
        if ($nodes instanceof SplList) {
            return $nodes;
        } elseif (is_array($nodes)) {
            return new SplList($nodes);
        }
        return new SplList();
    }

    public function nodes() {
        return $this->getNodeStack();
    }

    public function getParam($paramName) {
        if ($paramName == 'nodes') {
            if (!isset($this->nodes) || !is_array($this->nodes)) {
                $nodeProvider = $this->getLocator()->get('nodeProvider');
                $this->nodes = $nodeProvider->getNodesByPageId($this->getParam('page_id'));
            }
        }
        return $this->$paramName;
    }
    
    public function layout()
    {
        
    }

    public function getLocator() {
        return $this->locator;
    }

    public function setLocator(Locator $locator) {
        $this->locator = $locator;
        return $this;
    }
    
    public function serialize(){
        unset($this->locator);
        $serialized = parent::serialize();
        return $serialized;
    }

    
}
