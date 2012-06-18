<?php
namespace Page\Model;

use Zend\EventManager\Event;

/**
 * Description of Event
 *
 * @author Fam. Van Winden
 */
class ModelEvent extends Event
{
    
    public function setResult($result)
    {
        $this->setParam('result', $result);
        return $this;
    }
    
    public function getResult()
    {
        return $this->getParam('result');
    }
    
    public function setSucceed($succeed)
    {
        if(is_int($succeed)){
            $succeed = ($succeed > 0) ? true : false;
        }        
        
        $this->setParam('succeed', (bool) $succeed);
        return $this;
    }
    
    public function isSucceed(){
        return $this->getParam('succeed', false);
    }    
}
