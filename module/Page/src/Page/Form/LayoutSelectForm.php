<?php

namespace Page\Form;

use Zend\Form\Form,
    Page\Layout\Resolver;

/**
 * Description of LayoutSelectForm
 *
 * @author Fam. Van Winden
 */
class LayoutSelectForm extends Form {
        
    protected $layoutResolver;

    public function init() {
        
        $this->getPluginLoader(self::ELEMENT)->addPrefixPaths(array(
            'Page\Form\Element' => 'Page\Form\Element'                        
        ));
        
        $this->setOptions(array(
            'name' => 'layout-select',
            'elements' => array(
                'layout' => array(
                    'type' => 'layoutSelect'
                )
            )
        ));
    }

    public function setResolver(Resolver $layoutResolver) {
        $this->getElement('layout')->setResolver($layoutResolver);
        return $this;
    }

}
