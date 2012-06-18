<?php

namespace Text\Controller;

use Zend\Mvc\Controller\ActionController,
    Text\Form\TextSaveForm as TextSaveForm;

class TextController extends ActionController {

    public function getAction() {
        $locator = $this->getServiceLocator();
        $textProvider = $locator->get('textProvider');

        $e = $this->getEvent();

        $routeMatch = $e->getRouteMatch();
        $textId = $routeMatch->getParam('textid');

        $text = $textProvider->getTextById($textId);
        if (!$text) {
            $text = '';
        }

        return array(
            'text' => $text
        );
    }

    public function saveAction() {
        $request = $this->getRequest();

        //$this->layout(false);
        $pageStorage = $this->getServiceLocator()->get('pageStorage');
        $page = $pageStorage->get();

        $textSaveForm = new TextSaveForm($page);
        if ($request->isPost() && $textSaveForm->isValid($request->post()->toArray())) {
            $textSaveForm->save();
        }

        $accept = $request->headers()->get('accept');
        $accept = array_shift(explode(',', $accept->getFieldValue()));        
        if ($accept == 'application/json') {
            $this->view('text/save.json');
        } elseif ($accept == 'application/xml') {
            $this->view('text/save.xml');
        }

        return array(
            'textSaveForm' => $textSaveForm
        );
    }
}

