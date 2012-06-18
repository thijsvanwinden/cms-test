<?php

namespace Page\Controller;

use Zend\Mvc\Controller\ActionController,
    Hmvc\Dispatcher\DispatchStackAggregate,
    Page\Exception\PageNotFoundException,
    Page\Model\Page\Page,
    Page\Model\Page\Editable,
    Zend\EventManager\StaticEventManager;

class PageController extends ActionController {

    public function indexAction() {

        $layout = new \Page\Layout\Layout();
        $layout->addSlots(array(
            'header' => array(
                'type' => 'content',
            ),
            'content' => array(
                'type' => 'content'
            ),
            'footer' => array(
                'type' => 'content'
            )
        ));

        $stack = new \Page\Layout\Node\TreeNodeStack();
        $stack->addNodes(array(
            'header' => array(
                'type' => 'content',
                'options' => array(
                    'params' => array(
                        'contentid' => 1
                    )
                )
            ),
            'content' => array(
                'type' => 'content',
                'options' => array(
                    'params' => array(
                        'contentid' => 2
                    )
                )
            ),
            'footer' => array(
                'type' => 'content',
                'options' => array(
                    'params' => array(
                        'contentid' => 3
                    )
                )
            )
        ));

        var_dump($layout->isValid($stack));

        $filter = $layout->getSlot('header')->toValidatorFilter();
        var_dump($filter->filter($stack));
    }

    public function getAction() {
        $route = $this->getEvent()->getRouteMatch();

        $pageId = $route->getParam('pageid', 1);
        $page = $this->getPageById($pageId);

        $result = $this->dispatchPage($page);

        return array(
            'content' => $this->getEvent()->getParam("content")
        );
    }

    public function newAction() {        
        $locator = $this->getServiceLocator();
        
        $layoutSelectForm = $locator->get('layoutSelectForm');
        $layoutStack = $locator->get('layoutStack');
        
        $layoutSelectForm->setResolver($layoutStack);        
        
        return array(
            'form' => $layoutSelectForm
        );
    }

    public function saveAction() {
        $event = $this->getEvent();
        $request = $event->getRequest();

        $route = $event->getRouteMatch();

        $pageId = $route->getParam('pageid');
        $locator = $this->getServiceLocator();

        $pageStorage = $locator->get('pageStorage');
        $page = $pageStorage->get($pageId);

        if (!$page instanceof Editable) {
            throw new PageNotFoundException("Page should be an instanceof Editable.");
        }

        $nodeEditorBar = $locator->get('editorListener');
        $nodeEditorBar->registerStaticListeners(StaticEventManager::getInstance());

        $result = $this->dispatchPage($page);

        return array(
            'content' => $event->getParam("content"),
            'page' => $page
        );
    }

    public function commitAction() {
        $event = $this->getEvent();

        $route = $event->getRouteMatch();
        $pageId = $route->getParam('pageid');

        $pageStorage = $locator->get('pageStorage');
        if ($pageStorage->has($pageId)) {
            $pageStorage->commit();
        } else {
            throw new PageNotFoundException("No temporary page found in the storage.");
        }
    }

    public function rollbackAction() {
        $event = $this->getEvent();

        $route = $event->getRouteMatch();
        $pageId = $route->getParam('pageid');

        $pageStorage = $locator->get('pageStorage');
        if ($pageStorage->has($pageId)) {
            $pageStorage->rollback();
        } else {
            throw new PageNotFoundException("No temporary page found in the storage.");
        }
    }

    protected function dispatchPage($page) {
        if (!$page instanceof \Hmvc\Dispatcher\DispatchStackAggregate) {
            throw new PageNotFoundException(sprintf("Page with id: %s couldn't be found.", $pageId));
        }

        $locator = $this->getServiceLocator();
        $viewScriptSwitcher = $locator->get('viewScriptSwitcher');
        $viewScriptSwitcher->setViewScriptStack($page->getViewScriptStack());

        $actionStack = $this->actionStack();

        $dispatchStack = $page->getDispatchStack();
        $actionStack->setDispatchStack($dispatchStack);

        $this->getEvent()->setParam('page', $page);
        $result = $actionStack->dispatch();

        return $result;
    }

    protected function getPageById($pageId) {
        $locator = $this->getServiceLocator();
        $pageProvider = $locator->get('pageProvider');
        $page = $pageProvider->getPageById($pageId);

        if (!$page) {
            throw new PageNotFoundException(sprintf("Page with id: %s couldn't be found.", $pageId));
        }
        return $page;
    }

    public function deleteAction() {
        return array();
    }

    public function advertisementAction() {
        return array();
    }

    public function bannerAction() {
        return array();
    }

    public function serviceAction() {
        return array();
    }

    public function contactAction() {
        return array();
    }

    public function testAction() {

//        VersionResolver->last();
//        
//        VersionResolver->commit();        
//        
//        VersionResolver->first();
//        
//        VersionResolver->rollback();
//        
//        VersionResolver->open();
//        VersionResolver->mark();
//        
//        PageStorage->get($pageId)
//                VersionResolver->last();
//                if(user){
//                    VersionResolver->open();
//                } else {
//                    VersionResolver->mark();
//                }
//                
//        PageStorage->commit();
//        
    }

}

//Nieuw 
//    Nieuw pagina inserten in db
//
//Save
//    Laden bepaalde versie van pagina
//    Kopieeren en nieuwe aanmaken
//    Nieuwe pagina laden
//
//Node Nieuw
//    Bewerkte versie laden uit db
//    Mogelijke nodes laden
//    Kiezen
//    Opslaan in db bij page version
//
//Node Edit
//    Bewerkte versie laden uit db
//    Node edit action
//    Opslaan in db bij huidige page version
//
//Node Delete
//    Bewerkte versie laden uit db
//    Node verwijderen uit db of koppeling verwijderen uit db
//    
//Content Save
//Content Delete

    
    

/*
interfaces
Block\Header
Block\Navigation
Block\Footer
Block\Main
Block\Stack
Block\Layout
Block\Slot
valid
addType


classes
Block\PlaceHolderBlock
Block\StaticBlock
Block\TextBlock
Block\ActionBlock
Block\HtmlBlock
Block\CallbackBlock
Block\TreeBlockStack
Block\SimpleBlockStack

Block\Layout: Block\TreeBlockStack


$block->dispatch($request, $response);
$block->setView($view);
$block->render($vars);

$layout->setViewScript('layout')
$layout->setSlots(array(
    'header' => array(
        'types' => array(
            'content'
        )
    ),
    'navigation' => array(
        'types' => array(
            'stack'
        )
    ),
    'main' => array(
        'types' => array(
            'stack' => array(
                ''
            ),
            'content'
        )
    ),
    'footer' => array(
        'types' => array(
            'stack',
            'content'
        )
    )
));

$layout->setTheme('default');
$layout->addBlocks(array(
    'header' => array(
        'type' => 'content',
        'options' => array(
            'content' => 'blabla',
        )
    ),
    'navigation' => array(
        'type' => 'navigation',
        'options' => array(
            'blocks' => array(
                'main' => array(
                    'type' => 'treestack',
                    'options' => array(
                        'content' => 'test',
                        ''
                    )                    
                )                
            )
        )
    )
));
$layout->setView($view);
$layout->render($vars);

$page->setGrouping($layout);
$page->dispatch($request, $response);
$page->setView($view);
$page->render($vars);
$page->getRoute();

*/