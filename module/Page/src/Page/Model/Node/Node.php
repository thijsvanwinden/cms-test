<?php
namespace Page\Model\Node;

/**
 *
 * @author Fam. Van Winden
 */
interface Node {
    public function getNodeId();
    
    public function getName();
    
    public function getParams();
}
