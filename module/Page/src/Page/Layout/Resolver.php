<?php
namespace Page\Layout;
/**
 *
 * @author Fam. Van Winden
 */
interface Resolver {
    
    public function getAllLayouts();
    
    public function resolve($layoutName);
}
