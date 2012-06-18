<?php

namespace Hmvc\Dispatcher;

use Zend\Stdlib\Dispatchable;

/**
 * Description of DispatchStack
 *
 * @author Fam. Van Winden
 */
interface DispatchStack {

    /**
     * Add dispatchable
     * 
     * @param Dispatchable The dispatchable object
     * 
     * @return DispatchStack
     */
    public function addDispatchable($name, $dispatchable, $priority=null);

    /**
     * Add dispatchables
     * 
     * @param array $dispatchables An array of dispatchables
     * 
     * @return DispatchStack
     */
    public function addDispatchables($dispatchables);
    
    /**
     * Remove a dispatchable
     * 
     * @param string $name The name of the dispatchable
     * @return DispatchStack
     */
    
    public function removeDispatchable($name);
}

