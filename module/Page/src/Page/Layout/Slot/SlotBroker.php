<?php

namespace Page\Layout\Slot;

use Zend\Loader\PluginBroker;

/**
 * Description of SlotBroker
 *
 * @author Fam. Van Winden
 */
class SlotBroker extends PluginBroker {

    public function load($plugin, array $options = null) {
        $pluginName = strtolower($plugin);

        if (class_exists($plugin)) {
            // Allow loading fully-qualified class names via the broker
            $class = $plugin;
        } else {
            // Unqualified class names are then passed to the class loader
            $class = $this->getClassLoader()->load($plugin);

            if (empty($class)) {
                throw new Exception\RuntimeException('Unable to locate class associated with "' . $pluginName . '"');
            }
        }
        return $class::factory($options);
    }

}
