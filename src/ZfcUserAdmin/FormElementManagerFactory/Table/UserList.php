<?php
/**
 * Created by PhpStorm.
 * User: Clayton Daley
 * Date: 1/31/2015
 * Time: 1:06 PM
 */

namespace ZfcUserAdmin\FormElementManagerFactory\Table;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DaleyTable\StaticRow;
use ZfcUserAdmin\Table;

class UserList implements FactoryInterface {

    function createService(ServiceLocatorInterface $formElementManager) {
        /** @var ServiceLocatorInterface $serviceLocator */
        $serviceLocator = $formElementManager->getServiceLocator();

        /** @var $zfcUserAdminOptions \ZfcUserAdmin\Options\ModuleOptions */
        $zfcUserAdminOptions = $serviceLocator->get('zfcuseradmin_module_options');

        $row_prototype = new StaticRow();
        $row_prototype->setHydrator($serviceLocator->get('zfcuser_user_hydrator'));

        $table = new Table\UserList(null, $row_prototype, $zfcUserAdminOptions);
        // Inject the FormElementManager to support custom FormElements
        $table->getFormFactory()->setFormElementManager($formElementManager);

        return $table;
    }
}