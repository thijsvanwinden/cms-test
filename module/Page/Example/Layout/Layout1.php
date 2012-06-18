<?php

/**
 * Description of Layout
 *
 * @author Fam. Van Winden
 */
class Layout extends TreeSlotStack
{    
    public function validate(TreeNodeStack $nodes)
    {
        $result = clone $this->getResult();
        
        $slots = $this->slots;
        foreach($slots as $name => $slot){            
            if(!$slot instanceof Slot){
                throw new DomainException();
            }
                       
            $node = $nodes->getNode($name);                                    
            
            if(!$node instanceof Node){
                throw new DomainException();
            }
            
            $result[$name] = $slot->validate($node); 
        }
        
        return $result;
    }
        
}
