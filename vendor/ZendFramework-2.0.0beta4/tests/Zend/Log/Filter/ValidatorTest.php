<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Log\Filter;

use Zend\Log\Logger,
    Zend\Log\Filter\Validator,
    Zend\Validator\ValidatorChain,
    Zend\Validator\Alnum,
    Zend\Validator\Int;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Log
 */
class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testRecognizesInvalidValidator()
    {
        $this->setExpectedException('Zend\Log\Exception\InvalidArgumentException', 'Expected Zend\Validator object');
        new Validator('invalid');
    }

    public function testValidatorFilter()
    {
        $filter = new Validator(new Alnum());
        $this->assertTrue($filter->filter(array('message' => '123')));
        $this->assertTrue($filter->filter(array('message' => 'test')));
        $this->assertTrue($filter->filter(array('message' => 'test123')));
        $this->assertFalse($filter->filter(array('message' => '(%$')));
    }
    
    public function testValidatorChain()
    {
        $validatorChain = new ValidatorChain();
        $validatorChain->addValidator(new Alnum());
        $validatorChain->addValidator(new Int());
        $filter = new Validator($validatorChain);
        $this->assertTrue($filter->filter(array('message' => '123')));
        $this->assertFalse($filter->filter(array('message' => 'test')));
    }
}
