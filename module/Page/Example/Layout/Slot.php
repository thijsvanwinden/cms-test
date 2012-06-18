<?php

/**
 * Description of Slot
 *
 * @author Fam. Van Winden
 */
class Slot {
    
    public function validate($slot)
    {
        
    }
    
    public function toFilter()
    {
        return new ValidatorFilter($this->getValidatorChain());        
    }
    
}
