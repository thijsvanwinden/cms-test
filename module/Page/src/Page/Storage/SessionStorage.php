<?php

namespace Page\Storage;

use Zend\Session\SessionManager,
    Zend\Session\Container as SessionContainer,
    Page\Provider\PageProvider,
    Page\Model\Page\Page,
    Page\Exception\DomainException;

/**
 * Description of SessionStorage
 *
 * @author Fam. Van Winden
 */
class SessionStorage implements Storage, Revertable {
    /**
     * Default session namespace
     */
    const NAMESPACE_DEFAULT = 'Page_Storage';

    /**
     * Default session object member name
     */
    const MEMBER_DEFAULT = 'temp';

    /**
     * Object to proxy $session storage
     *
     * @var Zend\Session\Container
     */
    protected $session;

    /**
     * Session namespace
     *
     * @var mixed
     */
    protected $namespace;

    /**
     * Session object member
     *
     * @var mixed
     */
    protected $member;

    /**
     * The page provider object
     * 
     * @var Page\Provider\PageProvider
     */
    protected $pageProvider;

    /**
     * Sets session storage options and initializes session namespace object
     *
     * @param  mixed $namespace
     * @param  mixed $member
     * @return void
     */
    public function __construct(
    $namespace = self::NAMESPACE_DEFAULT, $member = self::MEMBER_DEFAULT, SessionManager $manager = null
    ) {
        if (!$namespace) {
            $namespace = self::NAMESPACE_DEFAULT;
        }
        if (!$member) {
            $member = self::MEMBER_DEFAULT;
        }

        $this->namespace = $namespace;
        $this->member = $member;
        $this->session = new SessionContainer($this->namespace, $manager);
    }

    public function commit($pageId=null) {
        if ($this->has($pageId)) {
            $this->getPageProvider()->save($this->get($pageId));
            $this->clear($pageId);
        } else {
            throw new DomainException("No page temporay page set.");
        }
    }

    public function rollback($pageId=null) {
        if ($this->has($pageId)) {
            $this->clear($pageId);
        } else {
            throw new DomainException("No page temporay page set.");
        }
    }

    public function clear($pageId = null) {
        if ($this->has($pageId)) {
//            if (!$pageId) {
                unset($this->session->{$this->member});
//            } else {
//                unset($this->session->{$pageId});
//            }
            return true;
        }
        return false;
    }

    public function get($pageId = null) {
        
        if (!$this->has($pageId)) {
            if ($pageId) {
                $page = $this->getPageProvider()->getPageById($pageId);
                $this->set($page);
            } else {
                return;
            }
        }
        
//        if (!$pageId) {
            $page = $this->session->{$this->member};
//        } else {
//            $page = $this->session->{$pageId};
//        }
        
        if ($page instanceof Page) {
            return $page;
        }
        throw new DomainException("The page in the storage must be an instance of Page");
    }

    public function has($pageId = null) {
//        if (!$pageId) {
            $page = $this->session->{$this->member};
//        } else {
//            $page = $this->session->{$pageId};
//        }
        
        if ($page instanceof Page) {
            return true;
        }
        return false;
    }

    public function set($page) {
        if (!$page instanceof Page) {
            throw new DomainException("The page in the storage must be an instance of Page");
        }
        $pageId = $page->getPageId();

        //if (!$pageId) {
            $this->session->{$this->member} = $page;
        //} //else {
         //   $this->session->{$pageId} = $page;
        //}
        return $this;
    }

    public function setPageProvider(PageProvider $pageProvider) {
        $this->pageProvider = $pageProvider;
        return $this;
    }

    public function getPageProvider() {
        return $this->pageProvider;
    }

}