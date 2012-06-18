<?php

namespace Page\Layout\Slot;

use Page\Layout\Filter\ValidatorFilter;

/**
 * Description of ContentSlot
 *
 * @author Fam. Van Winden
 */
class ContentSlot extends AbstractSlot {
    public static function factory($options) {
        $slot = parent::factory($options);

        $slot->addValidators(array(
            'slot\type' => array(
                'content'
            )
        ));

        return $slot;
    }

    public function isValid($value) {
        return $this->getValidatorChain()->isValid($value);
    }

    public function toValidatorFilter() {
        return new ValidatorFilter(array(
                    'validatorChain' => $this->getValidatorChain()
                ));
    }

}
