<?php
/**
 * @namespace
 */
namespace Hmvc\Dispatcher;

use Countable,
    Iterator,
    Zend\Stdlib\Dispatchable;

/**
 * Priority list
 * 
 * @todo FIX sort function
 *
  */
class PriorityList implements Iterator, Countable
{
    /**
     * Internal list of all dispatchables.
     *
     * @var array
     */
    protected $dispatchables = array();

    /**
     * Serial assigned to dispatchables to preserve LIFO.
     * 
     * @var integer
     */
    protected $serial = 0;

    /**
     * Internal counter to avoid usage of count().
     *
     * @var integer
     */
    protected $count = 0;

    /**
     * Whether the list was already sorted.
     *
     * @var boolean
     */
    protected $sorted = false;

    /**
     * Insert a new route.
     *
     * @param  string  $name
     * @param  Dispatchable   $dispatchable
     * @param  integer $priority
     * @return void
     */
    public function insert($name, Dispatchable $dispatchable, $priority)
    {
        $this->sorted = false;
        $this->count++;

        $this->dispatchables[$name] = array(
            'route'    => $dispatchable,
            'priority' => $priority,
            'serial'   => $this->serial++,
        );
    }

    /**
     * Remove a route.
     *
     * @param  string $name
     * @return void
     */
    public function remove($name)
    {
        if (!isset($this->dispatchables[$name])) {
            return;
        }
        
        $this->count--;

        unset($this->dispatchables[$name]);
    }
    
    /**
     * Get a route.
     * 
     * @param  string $name 
     * @return Dispatchable
     */
    public function get($name)
    {
        if (!isset($this->dispatchables[$name])) {
            return null;
        }
        
        return $this->dispatchables[$name]['route'];
    }

    /**
     * Sort all dispatchables.
     *
     * @return void
     */
    protected function sort()
    {
        uasort($this->dispatchables, array($this, 'compare'));
        $this->sorted = true;
    }

    /**
     * Compare the priority of two dispatchables.
     *
     * @param  array $dispatchable1,
     * @param  array $dispatchable2
     * @return integer
     */
    protected function compare(array $dispatchable1, array $dispatchable2)
    {
        if ($dispatchable1['priority'] === $dispatchable2['priority']) {
            return ($dispatchable1['serial'] > $dispatchable2['serial'] ? -1 : 1);
        }

        return ($dispatchable1['priority'] > $dispatchable2['priority'] ? -1 : 1);
    }

    /**
     * rewind(): defined by Iterator interface.
     *
     * @see    Iterator::rewind()
     * @return void
     */
    public function rewind() 
    {
        if (!$this->sorted) {
            $this->sort();
        }

        reset($this->dispatchables);
    }

    /**
     * current(): defined by Iterator interface.
     *
     * @see    Iterator::current()
     * @return Dispatchable
     */
    public function current() 
    {
        $node = current($this->dispatchables);
        return ($node !== false ? $node['route'] : false);
    }

    /**
     * key(): defined by Iterator interface.
     *
     * @see    Iterator::key()
     * @return string
     */
    public function key() 
    {
        return key($this->dispatchables);
    }

    /**
     * next(): defined by Iterator interface.
     *
     * @see    Iterator::next()
     * @return Dispatchable
     */
    public function next() 
    {
        $node = next($this->dispatchables);
        return ($node !== false ? $node['route'] : false);
    }

    /**
     * valid(): defined by Iterator interface.
     *
     * @see    Iterator::valid()
     * @return boolean
     */
    public function valid() 
    {
        return ($this->current() !== false);
    }

    /**
     * count(): defined by Countable interface.
     *
     * @see    Countable::count()
     * @return integer
     */
    public function count() 
    {
        return $this->count;
    }
}
