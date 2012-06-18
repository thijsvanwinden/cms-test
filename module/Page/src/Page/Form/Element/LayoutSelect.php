<?php
namespace Page\Form\Element;

use Zend\Form\Element\Radio,
    Page\Layout\Resolver;
/**
 * Description of LayoutSelect
 *
 * @author Fam. Van Winden
 */
class LayoutSelect extends Radio
{
    //public $helper = 'formlayoutselect';
    protected $resolver;
    
    public function setResolver(Resolver $resolver){
        $this->resolver = $resolver;
        $this->populate();
        return $this;
    }
    
    public function getResolver() {
        return $this->resolver;
    }

        
    public function populate(){
        $resolver = $this->getResolver();        
        $layouts = $resolver->getAllLayouts();
        
        $options = array();
        foreach($layouts as $name => $layout){
            $options[$name] = $layout;
        }
        
        //$this->addMultiOptions($options);
    }
    
}
