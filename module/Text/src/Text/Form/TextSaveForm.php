<?php

namespace Text\Form;

use Zend\Form\Form;

/**
 * Description of SaveForm
 *
 * @author Fam. Van Winden
 */
class TextSaveForm extends Form {    
    protected $page;

    public function __construct($page) {
        $this->page = $page;        
        parent::__construct(null);
    }

    public function init() {
        $this->setOptions(array(
            'id' => 'content-save-form',
            'elements' => array(
                'content' => array(
                    'type' => 'textarea',
                    'options' => array(
                        'cols' => 80,
                        'rows' => 20
                    )
                ),
                'submit' => array(
                    'type' => 'submit'
                )
            )
        ));
    }
    
    public function isValid($data)
    {
        if(parent::isValid($data) === false)
        {
            return false;
        }
        
        $node = $this->getNode();        
        $position = $this->getValue('position'); 
        
        $page->layout()->addNode($position, $node);
        $result = $page->layout()->validate();
        
        if($result->isValid()){
            return true;
        }
        
        $this->getElement('content')->addErrorMessages($result->getErrorMessages())
                                    ->addErrors($result->getErrors());
    }
    
    public function save($pageStorage)
    {        
        $pageStorage->commit($this->getPage());
    }
    
    public function getPage()
    {
        return $this->page;
    }
    
    public function getNode()
    {
        $layout = $this->getPage()->layout();
        $position = $this->getPosition();
        
        if($layout->hasNode($position)){
            $node = $layout->getNode($position);
            if($node instanceof ContentNode){
                
            }
        }
    }
    
    public function getPosition()
    {
        return $this->getValue('position');
    }

}
