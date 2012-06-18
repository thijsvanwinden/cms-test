<?php
namespace Page\Model\Node;

/**
 * Description of SimpleNodeStack
 *
 * @author Fam. Van Winden
 */
abstract class SimpleNodeStack extends SplList implements NodeStack
{
    public function addNode($nodeName, $node) {
        $this->set($nodeName, $node);        
    }

    public function addNodes($nodes) {
        
    }

    public function getNode($nodeName) {
        
    }

    public function getNodes() {
        
    }

    public function hasNode($nodeName) {
        
    }

    public function removeNode($nodeName) {
        
    }

}
