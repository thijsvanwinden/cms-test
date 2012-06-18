<?php

namespace Page\Controller;

use Zend\Mvc\Controller\ActionController,
    Hmvc\Dispatcher\DispatchStackAggregate,
    Page\Exception\PageNotFoundException,
    Page\Model\Page\Page,
    Page\Model\Page\Editable,
    Zend\Mvc\Router\RouteMatch;

class NodeController extends ActionController {

    public function getAction() {
        $e = $this->getEvent();
        $routeMatch = $e->getRouteMatch();
        $nodeId = $routeMatch->getParam('nodeid');

        $nodeProvider = $this->getServiceLocator()->get("nodeProvider");
        $node = $nodeProvider->getNodeById($nodeId);

        $this->actionStack()->add($node->toDispatchable());
        $result = $this->actionStack()->dispatch();

        return array(
            'content' => $e->getParam('content')
        );
    }

    public function saveAction() {
        $e = $this->getEvent();
        $routeMatch = $e->getRouteMatch();
        $name = $routeMatch->getParam('name');

        $page = $this->getPage();

        $node = $page->nodes()->get($name);
        if (!$node) {
            throw new PageNotFoundException("Node does not exists in the page.");
        }

        $route = $node->getEditRoute();
        return $this->redirect()->toRoute($route->getMatchedRouteName(), $route->getParams());
        return $this->forward()->dispatch($route->getParam('controller'), $route->getParams());
    }

    public function newAction() {
        $e = $this->getEvent();
        $routeMatch = $e->getRouteMatch();
        $nodeid = $routeMatch->getParam('nodeid');

        $page = $this->getPage();

        $request = $this->getRequest();
        $nodeSelectForm = new NodeSelectForm($nodeid, $page);
        if ($request->isPost() && $form->isValid($request->post())) {
            $nodeSelectForm->save();
        }

        $accept = $request->headers()->get('accept');
        $accept = array_shift(explode(',', $accept->getFieldValue()));
        if ($accept == 'application/json') {
            $this->view('content/save.json');
        } elseif ($accept == 'application/xml') {
            $this->view('content/save.xml');
        }

        return array(
            'nodeSelectForm' => $nodeSelectForm
        );
    }

    public function deleteAction() {
        $request = $this->getRequest();
        $e = $this->getEvent();
        $routeMatch = $e->getRouteMatch();
        $nodeId = $routeMatch->getParam('nodeid');

        $nodeDeleteForm = new NodeDeleteForm($nodeid, $page);
        if ($request->isPost() && $form->isValid($request->post()->toArray())) {
            $form->delete();
        }

        $accept = $request->headers()->get('accept');
        $accept = array_shift(explode(',', $accept->getFieldValue()));
        if ($accept == 'application/json') {
            $this->view('content/save.json');
        } elseif ($accept == 'application/xml') {
            $this->view('content/save.xml');
        }

        return array(
            'nodeDeleteForm' => $nodeDeleteForm
        );
    }

    protected function getPage() {
        $pageStorage = $this->getServiceLocator()->get("pageStorage");
        $page = $pageStorage->get();

        if (!$page) {
            throw new PageNotFoundException("No page is currently edited.");
        }
        return $page;
    }

}
