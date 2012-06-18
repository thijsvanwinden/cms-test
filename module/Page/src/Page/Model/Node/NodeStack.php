<?php

namespace Page\Model\Node;

/**
 *
 * @author Fam. Van Winden
 */
interface NodeStack {

    public function getNode($nodeName);

    public function getNodes();

    public function hasNode($nodeName);

    public function addNode($nodeName, $node);

    public function addNodes($nodes);

    public function removeNode($nodeName);
}
