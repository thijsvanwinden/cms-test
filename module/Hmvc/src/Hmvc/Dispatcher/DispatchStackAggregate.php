<?php
namespace Hmvc\Dispatcher;

/**
 *
 * @author Fam. Van Winden
 */
interface DispatchStackAggregate {
   
    /**
     * Get the dispatch stack from the aggregate
     * 
     * @return DispatchStack
     */
    
    public function getDispatchStack();
}

