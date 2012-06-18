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
    Traversable;

/**
 * Description of TextModel
 *
 * @author Fam. Van Winden
 */
class TextModel implements ListenerAggregate, Model, Mapper {

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
        $this->listeners[] = $events->attach('getContentById', array($this, 'getContentById'), 100);
        $this->listeners[] = $events->attach('getContentById', array($this, 'map'), -50);
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

    public function getContentById(Event $e) {
        $select = $this->getDbAdapter()->select();

        $select->from('page_contents')->where('content_id = ?', $e->getParam('content_id'));

        $result = $select->query();
        $result = $result->fetch();

        $e->setResult($result);
        return $result;
    }

    public function save(Event $e) {
        $dbAdapter = $this->getDbAdapter();
        $content = $e->getParam('content');

        if (!isset($page['content_id'])) {
            $result = $dbAdapter->insert('page_contents', $content);
            $page['content_id'] = $dbAdapter->lastInsertId('page_contents');
        } else {
            $result = $dbAdapter->update('page_contents', $content, array('content_id = ?' => $content['content_id']));
        }

        $e->setResult($content)
                ->setSucceed($result);
        return $content;
    }

    public function delete(Event $e) {
        $dbAdapter = $this->getDbAdapter();
        $pageId = $e->getParam('contentId');

        $result = $dbAdapter->delete('page_contents', array('content_id = ?' => $pageId));

        $e->setResult($result);
        return $result;
    }

    public function map(Event $e) {
        $result = $e->getResult();
        $e->setResult($result['content']);
        return $result['content'];
        if (!is_array($result)) {
            return;
        }

        $locator = $this->getLocator();

        if (!IsAssocArray::test($result)) {
            $resultData = array();
            foreach ($result as $entityData) {
                $pageEntity = $locator->get('contentEntity');
                $pageEntity->exchangeArray($entityData);
                $resultData[] = $pageEntity;
            }
        } else {
            $pageEntity = $locator->get('contentEntity');
            $pageEntity->exchangeArray($result);
            $resultData = $pageEntity;
        }

        $e->setResult($resultData);
        return $resultData;
    }

    public function unmap(Event $e) {
        $result = $e->getParam('content');
        if (!$result instanceof Carrier) {
            return;
        }

        $result = $result->getArrayCopy();
        $e->setParam('content', $result);
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

