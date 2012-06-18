<?php
namespace Page\Storage;
/**
 *
 * @author Fam. Van Winden
 */
interface Storage {
    
    public function set($page);
    
    public function get($pageId = null);
    
    public function has($pageId = null);   
    
    public function clear($pageId = null);    
    
    public function commit();
}
    