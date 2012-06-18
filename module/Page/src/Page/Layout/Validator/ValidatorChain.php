<?php

namespace Page\Layout\Validator;

use Zend\Validator\Validator,
    Zend\Validator\ValidatorChain as ZendValidatorChain,
    Zend\Validator\ValidatorBroker,
    Zend\Loader\Broker;

/**
 * Description of ValidatorChain
 *
 * @author Fam. Van Winden
 */
class ValidatorChain extends ZendValidatorChain {

    protected $broker;

    public function __construct($options = array()) {
        $this->setOptions($options);
        
        $this->getBroker()->getClassLoader()->registerPlugins(array(
            'slot\type' => 'Page\Layout\Validator\Slot\Type'
        ));
    }

    public function setOptions($options) {
        if (!is_array($options) && !$options instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf("Argument must be a instanceof Traversable or an array, %s given.", gettype($stack)));
        }

        if (isset($options['validators'])) {
            $this->addValidators($options['validators']);
        }
        return $this;
    }

    public function addValidators($validators) {
        foreach ($validators as $validatorName => $validatorOptions) {  
            if($validatorOptions instanceof Validator){
                $validatorName = $validatorOptions;
                $breakOnFailure = false;
            } elseif(is_array($validatorOptions)){
                $breakOnFailure = isset($validatorOptions['breakOnFailure']) ? $validatorOptions['breakOnFailure'] : false;
                if(isset($validatorOptions['instance']) && $validatorOptions['instance'] instanceof Validator){
                    $validatorName = $validatorOptions['instance'];
                }                    
            }
            $this->addValidator($validatorName, $validatorOptions, $breakOnFailure);
        }
        return $this;
    }

    public function addValidator($validatorName, $validatorOptions, $breakOnFailure = false) {
        if($validatorName instanceof Validator){
            $instance = $validatorName;
        } else {
            $instance = array(
                'name' => $validatorName,
                'options' => $validatorOptions
            );
        }
        
        $this->_validators[] = array(
            'instance' => $instance,
            'breakChainOnFailure' => (bool) $breakOnFailure
        );
        return $this;
    }
    
    public function getValidators()
    {
        foreach($this->_validators as $key => $validator)
        {
            if($validator['instance'] instanceof Validator){
                continue;
            }            
            $validator = $this->getBroker()->load($validator['instance']['name'], $validator['instance']['options']);
            $this->_validators[$key]['instance'] = $validator;
        } 
        return $this->_validators;
    }

    /**
     * Returns true if and only if $value passes all validations in the chain
     *
     * Validators are run in the order in which they were added to the chain (FIFO).
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value) {
        $this->getValidators();
        return parent::isValid($value);
    }

    /**
     * Get validator broker instance
     *
     * @return Zend\Loader\Broker
     */
    public function getBroker() {
        if (!$this->broker) {
            $this->setBroker(new ValidatorBroker());
        }
        return $this->broker;
    }

    /**
     * Set validator broker instance
     *
     * @param  string|Broker $broker Plugin broker to load slots
     * @return Zend\Loader\Pluggable
     */
    public function setBroker($broker) {
        
        if (!$broker instanceof Broker) {
            throw new Exception\InvalidArgumentException('Broker must implement Zend\Loader\Broker');
        }
        $this->broker = $broker;
        if (method_exists($broker, 'setBroker')) {
            $this->broker->setBroker($this);
        }
        return $this;
    }

}
