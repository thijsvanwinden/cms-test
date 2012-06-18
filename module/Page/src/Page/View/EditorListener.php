<?php
namespace Page\View;

use Zend\EventManager\StaticEventCollection,
    Zend\EventManager\Event,
    Zend\View\Renderer;
/**
 * Description of EditorListener
 *
 * @author Fam. Van Winden
 */
class EditorListener {
    
    protected $view;
    
    public function registerStaticListeners(StaticEventCollection $events)
    {
         $events->attach('Zend\Mvc\Controller\ActionController', 'dispatch', array($this, 'renderEditorBar'), -100);                 
    }
    
    public function renderEditorBar(Event $e)
    {
        $name = array_pop(explode('/', $e->getParam('name')));
        $page = $e->getParam('page');
        
        $view = $this->getView();
        
        $content = $view->placeholder($name);
        $editorBar = $view->nodeEditorBar(array(
            'new' => $view->url('content/new', array('name' => $name)),
            'edit'=> $view->url('content/save', array('name' => $name)),
            'delete' => $view->url('content/delete', array('name' => $name)),
        ));
        $view->placeholder($name)->set($editorBar.$content);
    }
    
    public function setView(Renderer $view){
        $this->view = $view;
        return $this;
    }
    
    public function getView(){
        return $this->view;
    }
    
}
