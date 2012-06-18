<?php

namespace Page\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Description of EditorBar
 *
 * @author Fam. Van Winden
 */
class NodeEditorBar extends AbstractHelper {

    public function __invoke($buttons) {
        $html = "<div class='container-option-bar'>";
        foreach ($buttons as $name => $buttonProps) {
            $html .= "<a class='container-option-bar-".$name."' href='".$buttonProps."'>".$name."</a> ";      
        }

        $html .= "</div>";
        return $html;
    }

}
