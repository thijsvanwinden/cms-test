<?php
namespace Page\Model\Node;
//namespace Maw\StdLib;

use ArrayObject,
    Serializable;

/**
 * Description of List
 *
 * @author Fam. Van Winden
 */
class SplList extends ArrayObject implements Serializable {

    /**
     * Constructor
     *
     * @param array $array 
     */
    public function __construct($array) {
        parent::__construct(array(), self::ARRAY_AS_PROPS);
        $this->addMultiple($array);
    }

    /**
     * Add a item to the list
     *
     * @param string $name
     * @param mixed $data
     * @return SplList 
     */
    public function add($name, $data) { 
        $this->{ (string) $name} = $data;
        return $this;
    }

    /**
     * Add multiple items to the list
     *
     * @param array $data
     * @return SplList 
     */
    public function addMultiple($data) {
        foreach ($data as $key => $value) {
            if(is_object($value) && method_exists($value, 'getName')){
                $key = $value->getName();
            }
            $this->add($key, $value);
        }
        return $this;
    }

    /**
     * Removes a item from the list
     *
     * @param string $name
     * @return SplList 
     */
    public function remove($name) {  
        if ($this->has($name)) {
            unset($this->{ (string) $name});
        }
        return $this;
    }

    /**
     * Gets a item from the list
     *
     * @param string $name
     * @return SplList 
     */
    public function get($name) {
        if($this->has($name)){
            return $this->{ (string) $name} ;
        }
        return null;
    }

    /**
     * Checks if the list has this property
     *
     * @param string $name
     * @return bool 
     */
    public function has($name) {
        return isset($this->{ (string) $name});
    }

    /**
     * Serialize to an array representing the stack
     * 
     * @return void
     */
    public function toArray() {
        $array = array();
        foreach ($this as $item) {
            $array[] = $item;
        }
        return $array;
    }

    /**
     * Serialize
     * 
     * @return string
     */
    public function serialize() {
        return serialize($this->toArray());
    }

    /**
     * Unserialize
     * 
     * @param  string $data
     * @return void
     */
    public function unserialize($data) {
        foreach (unserialize($data) as $item) {
            $this->unshift($item);
        }
    }

}

