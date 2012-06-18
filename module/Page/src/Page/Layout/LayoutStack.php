<?php

namespace Page\Layout;

use Page\Layout\Slot\SlotStack;
/**
 * Description of LayoutResolver
 *
 * @author Fam. Van Winden
 */
class LayoutStack implements Resolver {

    protected $layouts = array(
        'default' => array(
            'type' => 'Page\Layout\Layout',
            'options' => array(
            )
        )
    );

    public function addLayouts($layouts) {
        foreach ($layouts as $layoutName => $layout) {
            $this->addLayout($layoutName, $layout);
        }
        return $this;
    }

    public function addLayout($layoutName, $layout) {
        $this->layouts[$layoutName] = $layout;
        return $this;
    }

    public function removeLayout($layoutName) {
        if (isset($this->layouts[$layoutName])) {
            unset($this->layouts[$layoutName]);
            return true;
        }
        return false;
    }

    public function getAllLayouts() {
        return array_map(array($this, "getLayout"), array_keys($this->layouts));
    }

    public function getLayout($layoutName) {
        if (!$this->layouts[$layoutName] instanceof SlotStack) {
            $this->layouts[$layoutName] = $this->getLayoutFromArray($layoutName, $this->layouts[$layoutName]);
        }
        return $this->layouts[$layoutName];
    }

    public function resolve($layoutName) {
        return $this->getLayout($layoutName);
    }

    protected function getLayoutFromArray($layoutName, $layout) {
        if (!isset($layout['type'])) {
            throw new Exception\InvalidArgumentException(sprintf("No specific type given in layout with name : %s", $layoutName));
        }

        $layoutClass = $layout['type'];

        return $layoutClass::factory($layout);
    }

}
