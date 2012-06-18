<?php

namespace Page\Controller;

use Zend\Mvc\Controller\ActionController,
    Page\Form\ContentSaveForm as ContentSaveForm;

class ContentController extends ActionController {

    public function getAction() {
        $locator = $this->getServiceLocator();
        $contentProvider = $locator->get('contentProvider');

        $e = $this->getEvent();

        $routeMatch = $e->getRouteMatch();
        $contentId = $routeMatch->getParam('contentid');

        $content = $contentProvider->getContentById($contentId);
        if (!$content) {
            $content = '';
        }

        return array(
            'contents' => $content
        );
    }

    public function saveAction() {
        $request = $this->getRequest();

        //$this->layout(false);
        $pageStorage = $this->getServiceLocator()->get('pageStorage');
        $page = $pageStorage->get();

        $contentSaveForm = new ContentSaveForm($page);
        if ($request->isPost() && $contentSaveForm->isValid($request->post()->toArray())) {
            $contentSaveForm->save();
        }

        $accept = $request->headers()->get('accept');
        $accept = array_shift(explode(',', $accept->getFieldValue()));        
        if ($accept == 'application/json') {
            $this->view('content/save.json');
        } elseif ($accept == 'application/xml') {
            $this->view('content/save.xml');
        }

        return array(
            'contentSaveForm' => $contentSaveForm
        );
    }
}

