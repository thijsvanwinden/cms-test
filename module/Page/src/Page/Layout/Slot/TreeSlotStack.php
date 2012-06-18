<?php

namespace Page\Layout\Slot;

use ArrayObject,
    Zend\Loader\Broker;

/**
 * Description of TreeSlotStack
 *
 * @author Fam. Van Winden
 */
abstract class TreeSlotStack extends ArrayObject implements SlotStack, Slot {

    protected $broker;

    public function __construct() {
        $this->getBroker()->getClassLoader()->registerPlugins(array(
            'content' => 'Page\Layout\Slot\ContentSlot'
        ));
    }
    
    public static function factory($options)
    {
        return new static();
    }
    
    public function toValidatorFilter() {
        return new ValidatorFilter();
    }

    public function getSlot($slotName) {
        if (!$this[$slotName] instanceof Slot) {
            $this[$slotName] = $this->slotFromArray($this[$slotName]);
        }
        return $this[$slotName];
    }

    public function getSlots() {
        $slots = array();
        foreach($this as $slotName => $slot){
            if(!$slot instanceof Slot){
                $slot = $this->getSlot($slotName);    
            }
            $slots[$slotName] = $slot;
        }        
        
        return $slots;
    }

    public function addSlots($slots) {
        foreach ($slots as $slotName => $slot) {
            $this[$slotName] = $slot;
        }
        return $this;
    }

    public function slotFromArray($slot) {
        if (!is_array($slot)) {
            throw new Exception\InvalidArgumentException(sprintf("Argument must be an array, %s given.", gettype($stack)));
        }

        if (!isset($slot['type'])) {
            throw new Exception\InvalidArgumentException("Slot without type given. Please provide a type.");
        }

        $slot = $this->getBroker()->load($slot['type'], $slot);

        return $slot;
    }

    /**
     * Get slot broker instance
     *
     * @return Zend\Loader\Broker
     */
    public function getBroker() {
        if (!$this->broker) {
            $this->setBroker(new SlotBroker());
        }
        return $this->broker;
    }

    /**
     * Set slot broker instance
     *
     * @param  string|Broker $broker Plugin broker to load slots
     * @return Zend\Loader\Pluggable
     */
    public function setBroker($broker) {
        if (!$broker instanceof Broker) {
            throw new Exception\InvalidArgumentException('Broker must implement Zend\Loader\Broker');
        }
        $this->broker = $broker;
        if (method_exists($broker, 'setSlotStack')) {
            $this->broker->setSlotStack($this);
        }
        return $this;
    }

}
