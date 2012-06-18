<?php
namespace Page\Model;

use Page\Model\ModelEvent as Event;

/**
 *
 * @author Fam. Van Winden
 */
interface Mapper {
    public function map(Event $e);
    public function unmap(Event $e);
}
