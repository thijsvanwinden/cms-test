<?php
namespace Page\Layout\Node\Type;
/**
 * Description of ContentNode
 *
 * @author Fam. Van Winden
 */
class ContentNode extends ActionNode
{
    protected $params = array(
        'controller' => 'contentController',
        'action' => 'get'
    );
}
