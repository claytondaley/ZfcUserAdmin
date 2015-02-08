<?php

namespace ZfcUserAdmin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use ZfcUser\Mapper\UserInterface;
use ZfcUser\Options\ModuleOptions as ZfcUserModuleOptions;
use ZfcUserAdmin\Options\ModuleOptions;

class UserAdminController extends AbstractActionController
{
    protected $options, $userMapper;
    protected $zfcUserOptions;
    /**
     * @var \ZfcUserAdmin\Service\User
     */
    protected $adminUserService;

    public function listAction()
    {
        $userMapper = $this->getUserMapper();
        $users = $userMapper->findAll();
        if (is_array($users)) {
            $paginator = new Paginator(new ArrayAdapter($users));
        } else {
            $paginator = $users;
        }

        $paginator->setItemCountPerPage(100);
        $paginator->setCurrentPageNumber($this->getEvent()->getRouteMatch()->getParam('p'));

        // Must use the FormElementManager to support custom FormElements
        $formElementManager = $this->getServiceLocator()->get('FormElementManager');
        /** @var $table \ZfcUserAdmin\Table\UserList */
        $table = $formElementManager->get('ZfcUserAdmin\Table\UserList');

        // For rows that key to the ID, indicate which column provides that ID
        $table->setIdField('id');
        // Bind users to table
        $table->bind($paginator);

        return array(
            'table' => $table
        );
    }

    public function createAction()
    {
        // Must use the FormElementManager to support custom FormElements
        $formElementManager = $this->getServiceLocator()->get('FormElementManager');
        /** @var $form \ZfcUserAdmin\Form\CreateUser */
        $form = $formElementManager->get('zfcuseradmin_createuser_form');

        /** @var $request \Zend\Http\Request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            $user = $this->getUserMapper()->create();
            $form->bind($user);

            $form->setData($request->getPost());

            if ($form->isValid()) {
                $user = $this->getAdminUserService()->create($form, (array)$request->getPost(), $user);
                if ($user) {
                    $this->flashMessenger()->addSuccessMessage('The user was created');
                    return $this->redirect()->toRoute('zfcadmin/zfcuseradmin/list');
                }
            }
        }

        return array(
            'createUserForm' => $form
        );
    }

    public function editAction()
    {
        // Must use the FormElementManager to support custom FormElements
        $formElementManager = $this->getServiceLocator()->get('FormElementManager');
        /** @var $form \ZfcUserAdmin\Form\EditUser */
        $form = $formElementManager->get('zfcuseradmin_edituser_form');

        /** @var $request \Zend\Http\Request */
        $request = $this->getRequest();

        $userId = $this->getEvent()->getRouteMatch()->getParam('userId');
        $user = $this->getUserMapper()->findById($userId);
        $form->bind($user);

        // don't automatically overwrite password since input permit blank
        $form->getInputFilter()->remove('password');
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $user = $this->getAdminUserService()->edit($form, (array)$request->getPost(), $user);
                if ($user) {
                    $this->flashMessenger()->addSuccessMessage('The user was edited');
                    return $this->redirect()->toRoute('zfcadmin/zfcuseradmin/list');
                }
            }
        }
        // Necessary so validators know whether a username and email have changed since they may be a "duplicate" of
        // their own database record, but not of other users.
        $form->get('userId')->setValue($user->getId());

        return array(
            'editUserForm' => $form,
            'userId' => $userId
        );
    }

    public function removeAction()
    {
        $userId = $this->getEvent()->getRouteMatch()->getParam('userId');

        /** @var $identity \ZfcUser\Entity\UserInterface */
        $identity = $this->zfcUserAuthentication()->getIdentity();
        if ($identity && $identity->getId() == $userId) {
            $this->flashMessenger()->addErrorMessage('You can not delete yourself');
        } else {
            $user = $this->getUserMapper()->findById($userId);
            if ($user) {
                $this->getUserMapper()->remove($user);
                $this->flashMessenger()->addSuccessMessage('The user was deleted');
            }
        }

        return $this->redirect()->toRoute('zfcadmin/zfcuseradmin/list');
    }

    public function setOptions(ModuleOptions $options)
    {
        $this->options = $options;
        return $this;
    }

    public function getOptions()
    {
        if (!$this->options instanceof ModuleOptions) {
            $this->setOptions($this->getServiceLocator()->get('zfcuseradmin_module_options'));
        }
        return $this->options;
    }

    public function getUserMapper()
    {
        if (null === $this->userMapper) {
            $this->userMapper = $this->getServiceLocator()->get('zfcuser_user_mapper');
        }
        return $this->userMapper;
    }

    public function setUserMapper(UserInterface $userMapper)
    {
        $this->userMapper = $userMapper;
        return $this;
    }

    public function getAdminUserService()
    {
        if (null === $this->adminUserService) {
            $this->adminUserService = $this->getServiceLocator()->get('zfcuseradmin_user_service');
        }
        return $this->adminUserService;
    }

    public function setAdminUserService($service)
    {
        $this->adminUserService = $service;
        return $this;
    }

    public function setZfcUserOptions(ZfcUserModuleOptions $options)
    {
        $this->zfcUserOptions = $options;
        return $this;
    }

    /**
     * @return \ZfcUser\Options\ModuleOptions
     */
    public function getZfcUserOptions()
    {
        if (!$this->zfcUserOptions instanceof ZfcUserModuleOptions) {
            $this->setZfcUserOptions($this->getServiceLocator()->get('zfcuser_module_options'));
        }
        return $this->zfcUserOptions;
    }
}
