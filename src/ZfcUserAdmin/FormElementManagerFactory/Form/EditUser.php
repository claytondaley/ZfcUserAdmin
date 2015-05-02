<?php
/**
 * Created by PhpStorm.
 * User: Clayton Daley
 * Date: 1/31/2015
 * Time: 1:03 PM
 */

namespace ZfcUserAdmin\FormElementManagerFactory\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcUser\Form\RegisterFilter;
use ZfcUserAdmin\Form;
use ZfcUserAdmin\Validator\NoRecordExistsEdit;

class EditUser implements FactoryInterface {

    public function createService(ServiceLocatorInterface $formElementManager) {
        $serviceLocator = $formElementManager->getServiceLocator();

        /** @var $zfcUserOptions \ZfcUser\Options\UserServiceOptionsInterface */
        $zfcUserOptions = $serviceLocator->get('zfcuser_module_options');
        /** @var $zfcUserAdminOptions \ZfcUserAdmin\Options\ModuleOptions */
        $zfcUserAdminOptions = $serviceLocator->get('zfcuseradmin_module_options');
        $form = new Form\EditUser(null, $zfcUserAdminOptions, $zfcUserOptions, $serviceLocator);

        $form->setHydrator($serviceLocator->get('zfcuser_user_hydrator'));

        $filter = new RegisterFilter(
            new NoRecordExistsEdit(array(
                'mapper' => $serviceLocator->get('zfcuser_user_mapper'),
                'key' => 'email'
            )),
            new NoRecordExistsEdit(array(
                'mapper' => $serviceLocator->get('zfcuser_user_mapper'),
                'key' => 'username'
            )),
            $zfcUserOptions
        );
        if (!$zfcUserAdminOptions->getAllowPasswordChange()) {
            $filter->remove('password')->remove('passwordVerify');
        } else {
            $filter->get('password')->setRequired(false);
            $filter->remove('passwordVerify');
        }
        $form->setInputFilter($filter);
        return $form;
    }
}