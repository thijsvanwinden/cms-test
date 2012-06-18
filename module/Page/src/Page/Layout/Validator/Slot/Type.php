<?php
namespace Page\Layout\Validator\Slot;

use Zend\Validator\AbstractValidator,
    Page\Layout\Node\Type\ContentNode;
/**
 * Description of Type
 *
 * @author Fam. Van Winden
 */
class Type extends AbstractValidator {
    public function isValid($node)    
    {
        return $node instanceof ContentNode;
    }
}
