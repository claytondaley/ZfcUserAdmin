<?php
/**
 * Created by PhpStorm.
 * User: Clayton Daley
 * Date: 5/2/2015
 * Time: 3:59 PM
 */

namespace ZfcUserAdminTest\Form;

use ZfcUserAdmin\Form\CreateUser as Form;

class CreateUserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ZfcUserAdmin\Form\CreateUser::__construct
     * @dataProvider providerTestConstruct
     */
    public function testConstruct($autoPassword, $fields)
    {
        $createOptions = $this->getMock('ZfcUserAdmin\Options\ModuleOptions');
        $createOptions->expects($this->once())
            ->method('getCreateUserAutoPassword')
            ->will($this->returnValue($autoPassword));
        $createOptions->expects($this->once())
            ->method('getCreateFormElements')
            ->will($this->returnValue($fields));
        $registerOptions = $this->getMock('ZfcUser\Options\UserServiceOptionsInterface');
        $serviceManager = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');

        $form = new Form(null, $createOptions, $registerOptions, $serviceManager);

        $elements = $form->getElements();

        # email and submit are always included
        $this->assertArrayHasKey('email', $elements);
        $this->assertArrayHasKey('submit', $elements);

        # password and passwordVerify are contingent on the getCreateUserAutoPassword setting
        foreach (array('password', 'passwordVerify') as $pw) {
            if (!$autoPassword) {
                $this->assertArrayHasKey($pw, $elements);
            } else {
                $this->assertArrayNotHasKey($pw, $elements);
            }
        }

        # additional fields are contingent on getCreateFormElements array
        if (count($fields) > 0) {
            foreach (array('username', 'display_name') as $field) {
                if (in_array($field, $fields)) {
                    $this->assertArrayHasKey($field, $elements);
                } else {
                    $this->assertArrayNotHasKey($field, $elements);
                }
            }
        }
    }

    public function providerTestConstruct()
    {
        return array(
            array(true,     array()),
            array(true,     array('username')),
            array(true,     array('display_name')),
            array(true,     array('username', 'display_name')),
            array(false,    array()),
            array(false,    array('username')),
            array(false,    array('display_name')),
            array(false,    array('username', 'display_name')),
        );
    }
}