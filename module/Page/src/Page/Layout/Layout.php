<?php

namespace Page\Layout;

use Page\Layout\Node\NodeStack,
    Page\Layout\Slot\TreeSlotStack,
    Zend\Validator\Validator,
    Page\Layout\Validator\ValidatorChain,
    ArrayObject;

/**
 * Description of Layout
 *
 * @author Fam. Van Winden
 */
class Layout extends TreeSlotStack implements Validator {

    protected $validatorChain; 
    public function isValid($stack) {
        if (!$stack instanceof NodeStack) {
            throw new Exception\InvalidArgumentException(sprintf("Argument must be a instanceof NodeStack, %s given.", gettype($stack)));
        }
        //$validatorChain = $this->getValidatorChain();
        //return $validatorChain->isValid($stack);
        $slots = $this->getSlots();
        $result = true;
        foreach($slots as $slotName => $slot){
            $node = $stack->getNode($slotName);
            
            if(true !== $slot->isValid($node)){
                $result = false;
            }            
        }
        return $result;
    }

    public function getValidatorChain() {
        if ($this->validatorChain === null) {
            $this->validatorChain = new ValidatorChain(array(
                        'validators' => $this->getSlots()
                    ));
        }
        return $this->validatorChain;
    }
    
    public function getMessages()
    {
        return $this->getValidatorChain()->getMessages();
    }

}
