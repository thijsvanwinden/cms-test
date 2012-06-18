<?php
namespace Page\Layout\Slot;

use Zend\Validator\Validator;
/**
 *
 * @author Fam. Van Winden
 */
interface Slot extends Validator 
{    
    public static function factory($options); 
    
    public function toValidatorFilter();
    
}
