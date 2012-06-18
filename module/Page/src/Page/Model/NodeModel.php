<?php

namespace Page\Model;

use Zend\EventManager\EventCollection,
    Zend\EventManager\EventManager,
    Zend\EventManager\ListenerAggregate,
    Zend\Db\Adapter\AbstractAdapter,
    Zend\Stdlib\IsAssocArray,           
    Zend\Di\Locator,
    Zend\Loader\LocatorAware,                
    Page\Model\ModelEvent as Event,
    Page\Carrier,
    Traversable;
/**
 * Description of PageModel
 *
 * @author Fam. Van Winden
 */
class NodeModel implements ListenerAggregate, Model, Mapper {

    protected $listeners = array();
    protected $mapper = array();

    /**
     *
     * @var AbstractAdapter
     */
    protected $dbAdapter;

    public function setOptions($options) {
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    public function attach(EventCollection $events) {
        $this->listeners[] = $events->attach('getNodesByPageId', array($this, 'getNodesByPageId'), 100);
        $this->listeners[] = $events->attach('getNodesByPageId', array($this, 'map'), -50);
        $this->listeners[] = $events->attach('save', array($this, 'save'), 100);
        $this->listeners[] = $events->attach('save', array($this, 'unmap'), 150);
        $this->listeners[] = $events->attach('save', array($this, 'map'), -50);
    }

    public function detach(EventCollection $events) {
        foreach ($this->listeners as $listener) {
            $event->detach($listener);
        }
        return $this;
    }

    public function getNodesByPageId(Event $e) {
        $select = $this->getDbAdapter()->select();

        $select->from('page_nodes')->where('page_id = ?', $e->getParam('pageId'));
        $result = $select->query();
        $result = $result->fetchAll();

        $e->setResult($result);
        return $result;
    }

    public function save(Event $e) {
        $dbAdapter = $this->getDbAdapter();
        $node = $e->getParam('node');

        if (!isset($node['node_id'])) {
            $result = $dbAdapter->insert('page_nodes', $node);
            $page['node_id'] = $dbAdapter->lastInsertId('node_id');
        } else {
            $result = $dbAdapter->update('page_nodes', $node, array('node_id = ?' => $node['node_id']));
        }

        $e->setResult($node)
                ->setSucceed($result);
        return $node;
    }

    public function map(Event $e) {
        $result = $e->getResult();

        if (!is_array($result)) {
            return array();
            $e->setResult(array());
        }

        $locator = $this->getLocator();

        if (!IsAssocArray::test($result)) {
            $resultData = array();
            foreach ($result as $entityData) {
                if (isset($entityData['params'])) {
                    $entityData['params'] = (array) json_decode($entityData['params']);
                }

                $pageEntity = $locator->newInstance('nodeEntity');
                $pageEntity->exchangeArray($entityData);
                $resultData[] = $pageEntity;
            }
        } else {
            if (isset($result['params'])) {
                $result['params'] = (array) json_decode($result['params']);
            }
            $pageEntity = $locator->newInstance('nodeEntity');
            $pageEntity->exchangeArray($result);
            $resultData = $pageEntity;
        }
        
        $e->setResult($resultData);
        return $resultData;
    }

    public function unmap(Event $e) {
        $result = $e->getParam('node');
        if (!$result instanceof Carrier) {
            return;
        }

        $result = $result->getArrayCopy();

        if (is_array($result['params'])) {
            $result['params'] = json_encode($result['params']);
        }
        
        $e->setParam('node', $result);
        return $result;
    }

    public function getDbAdapter() {
        return $this->dbAdapter;
    }

    /**
     *
     * @param AbstractAdapter $dbAdapter
     * @return PageModel 
     */
    public function setDbAdapter(AbstractAdapter $dbAdapter) {
        $this->dbAdapter = $dbAdapter;
        return $this;
    }
    
        

    public function getLocator() {
        return $this->locator;        
    }

    public function setLocator(Locator $locator) {
        $this->locator = $locator;
        return $this;
    }

}
