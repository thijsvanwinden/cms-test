<?php

namespace Page\Model;

use Zend\EventManager\EventManagerInterface as EventCollection,
    Zend\EventManager\EventManager,
    Zend\EventManager\ListenerAggregateInterface as ListenerAggregate,
    Zend\Db\Adapter\AbstractAdapter,
    Zend\Stdlib\IsAssocArray,     
        Zend\Di\ServiceLocator
    Zend\ServiceManager\ServiceLocatorInterface as ServiceLocator,
    Zend\ServiceManager\ServiceLocatorAwareInterface as ServiceLocatorAware,           
    Page\Model\ModelEvent as Event,
    Traversable;

/**
 * Description of PageModel
 *
 * @author Fam. Van Winden
 */
class PageModel implements ListenerAggregate, Model, Mapper, ServiceLocatorAware {

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
        $this->listeners[] = $events->attach('getPageById', array($this, 'getPageById'), 100);
        $this->listeners[] = $events->attach('getPageById', array($this, 'map'), -50);
        $this->listeners[] = $events->attach('save', array($this, 'save'), 100);
        $this->listeners[] = $events->attach('save', array($this, 'unmap'), 150);
        $this->listeners[] = $events->attach('save', array($this, 'map'), -50);
        $this->listeners[] = $events->attach('delete', array($this, 'delete'));
    }

    public function detach(EventCollection $events) {
        foreach ($this->listeners as $listener) {
            $event->detach($listener);
        }
        return $this;
    }

    public function getPageById(Event $e) {
        $select = $this->getDbAdapter()->select();

        $select->from('page_pages')->where('page_id = ?', $e->getParam('pageId'));
        
        $result = $select->query();
        $result = $result->fetch();

        $e->setResult($result);
        return $result;
    }

    public function save(Event $e) {
        $dbAdapter = $this->getDbAdapter();
        $page = $e->getParam('page');

        if (!isset($page['page_id'])) {
            $result = $dbAdapter->insert('page_pages', $page);
            $page['page_id'] = $dbAdapter->lastInsertId('page_pages');
        } else {
            $result = $dbAdapter->update('page_pages', $page, array('page_id = ?' => $page['page_id']));
        }

        $e->setResult($page)
                ->setSucceed($result);
        return $page;
    }

    public function delete(Event $e) {
        $dbAdapter = $this->getDbAdapter();
        $pageId = $e->getParam('pageId');

        $result = $dbAdapter->delete('page_pages', array('page_id = ?' => $pageId));

        $e->setResult($result);
        return $result;
    }

    public function map(Event $e) {
        $result = $e->getResult();
        if (!is_array($result)) {
            return;
        }
        
        $locator = $this->getLocator();
        
        if (!IsAssocArray::test($result)) {
            $resultData = array();
            foreach ($result as $entityData) {
                $pageEntity = $locator->get('pageEntity');
                $pageEntity->exchangeArray($entityData);
                $resultData[] = $pageEntity;
            }
        } else {            
            $pageEntity = $locator->get('pageEntity');
            $pageEntity->exchangeArray($result);
            $resultData = $pageEntity;          
        }

        $e->setResult($resultData);
        return $resultData;
    }

    public function unmap(Event $e) {
        $result = $e->getParam('page');
        if (!$result instanceof Carrier) {
            return;
        }

        $result = $result->getArrayCopy();
        $e->setParam('page', $result);
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
    
    

    public function getServiceLocator() {
        return $this->locator;        
    }

    public function setServiceLocator(ServiceLocator $locator) {
        $this->locator = $locator;
        return $this;
    }

}
