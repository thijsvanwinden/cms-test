<?php
namespace Page\Layout\Node;
/**
 * Description of NodeResolver
 *
 * @author Fam. Van Winden
 */
class NodeResolver {

    public function get($name) {
        return $this->getBroker()->get($name);
    }

    public function getAll() {
        $broker = $this->getBroker();
        $nodes = $broker->getLoader()->getPlugins($name);
        
        $treeStack = new TreeNodeStack();
        
        foreach($nodes as $nodeName => $node){
            $treeStack[$nodeName] = $broker->load($node);
        }
        return $treeStack;
    }

    public function find(Filter $filter)
    {
        return array_shift($this->findALl($filter));
    }

    public function findAll(Filter $filter){
        return $filter->filter($this->getAll());
    }
}
