<?php
namespace Page\Storage;
/**
 *
 * @author Fam. Van Winden
 */
interface Revertable {    
    
    public function rollback();    
}
