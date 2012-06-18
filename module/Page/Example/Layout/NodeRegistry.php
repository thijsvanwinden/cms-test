<?php

/**
 * Description of NodeRegistry
 *
 * @author Fam. Van Winden
 */
class NodeStack extends \ArrayObject {
    public function __construct($data)
    {
        parent::__construct($data, self::ARRAY_AS_PROPS);
    }
    
    public function filter(Filter $filter)
    {
        
    }
    
        
}
