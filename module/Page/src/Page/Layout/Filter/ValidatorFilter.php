<?php

namespace Page\Layout\Filter;

use Zend\Filter\AbstractFilter,
    Page\Layout\Node\NodeStack,
    Page\Layout\Validator\ValidatorChain;

/**
 * Description of ValidatorFilter
 *
 * @author Fam. Van Winden
 */
class ValidatorFilter extends AbstractFilter {

    protected $validatorChain;

    public function __construct($options) {
        $this->setOptions($options);
    }

    public function setOptions($options) {
        if (!is_array($options) && !$options instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf("Argument must be a instanceof Traversable or an array, %s given.", gettype($stack)));
        }

        if (isset($options['validators'])) {
            $this->addValidators($options['validators']);
            unset($options['validators']);
        }

        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    public function filter($nodes) {
        if (!$nodes instanceof NodeStack) {
            throw new Exception\InvalidArgumentException(sprintf("Nodes should be an instanceof of NodeStack, %s given", get_type($nodes)));
        }

        $nodes = $nodes->getNodes();
        $result = array();
        foreach ($nodes as $nodeName => $node) {
            if ($node instanceof NodeStack) {
                $result[$nodeName] = $this->filter($node);
            } else {
                if ($this->getValidatorChain()->isValid($node)) {
                    $result[$nodeName] = $node;
                }
            }
        }
        return $result;
    }

    public function addValidators($validators) {
        $this->getValidatorChain()->addValidators($validators);
        return $this;
    }

    public function setValidatorChain(ValidatorChain $validatorChain) {
        $this->validatorChain = $validatorChain;
        return $this;
    }

    public function getValidatorChain() {
        if (!$this->validatorChain instanceof ValidatorChain) {
            $this->validatorChain = new ValidatorChain();
        }
        return $this->validatorChain;
    }

}
