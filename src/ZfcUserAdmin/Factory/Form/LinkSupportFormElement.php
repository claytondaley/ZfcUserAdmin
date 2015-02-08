<?php
/**
 * Created by PhpStorm.
 * User: Clayton Daley
 * Date: 2/20/2015
 * Time: 1:50 PM
 */

namespace ZfcUserAdmin\Factory\Form;


use Zend\Form\View\Helper\FormElement;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LinkSupportFormElement implements FactoryInterface {

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $helper = new FormElement();
        $helper->addType('link', 'formlink');
        $helper->addClass('Zend\Table\Element\Link', 'formlink');
        return $helper;
    }
}