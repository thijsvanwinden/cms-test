<?php
namespace Page\Layout\Slot;

use Traversable,
    Page\Layout\Validator\ValidatorChain;
/**
 * Description of AbstractSlot
 *
 * @author Fam. Van Winden
 */
abstract class AbstractSlot implements Slot
{    
    protected $validatorChain;
    
    public static function factory($options)
    {
        if(!is_array($options) && ! $options instanceof Traversable){
            throw new Exception\InvalidArgumentException(sprintf("Options should be an array or an instance of Traversable, %s given.", gettype($options)));
        }
        
        $slot = new static();
        
        if(isset($options['validators'])){
            $slot->addValidators($options['validators']);            
        }      
        
        if(isset($options['filters'])){
            $slot->addFilters($options['filters']);            
        }
        return $slot;
    }
    
    public function addValidators($validators)
    {
        $this->getValidatorChain()->addValidators($validators);
        return $this;
    }
    
    public function addFilters($filters)
    {
        $this->getFilterChain()->addFilters($filters);
        return $this;
    }
    
    public function getMessages()
    {
        return $this->getValidatorChain()->getMessages();
    }
    
    public function setValidatorChain(){}
    public function getValidatorChain(){
        if(!$this->validatorChain instanceof ValidatorChain){
            $this->validatorChain = new ValidatorChain();            
        }
        return $this->validatorChain;
    }
    
    public function setFilterChain(){}
    public function getFilterChain(){}
}
