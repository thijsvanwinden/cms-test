<?php

namespace Page\View\Helper;

use Zend\View\Helper\AbstractHelper,
    Zend\View\Helper\HeadScript;

/**
 * Description of TinyMce
 *
 * @author Fam. Van Winden
 */
class TinyMce extends AbstractHelper {
    
    public function __invoke($options)
    {
        $script = "";
        $this->getView()->inlineScript(HeadScript::SCRIPT, $script);
        return $this;
    }    
}
