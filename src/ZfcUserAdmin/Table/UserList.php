<?php
/**
 * Created by PhpStorm.
 * User: Clayton Daley
 * Date: 2/15/2015
 * Time: 1:11 PM
 */

namespace ZfcUserAdmin\Table;

use Zend\Table\KeyedCollection;
use Zend\Table\Table;
use Zend\Table\RowInterface;
use ZfcUserAdmin\Options\UserListOptionsInterface;

class UserList extends Table
{
    protected $userListOptions;

    public function __construct($name = null, RowInterface $rowPrototype, UserListOptionsInterface $userListOptions)
    {
        $this->setUserListOptions($userListOptions);
        parent::__construct($rowPrototype);
    }

    public function init()
    {
        foreach ($this->getUserListOptions()->getUserListElements() as $label => $name) {
            $this->add(
                array(
                    'type' => 'text',
                    'name' => $name,
                    'options' => array(
                        'label' => $label,
                    ),
                )
            );
        }

        /** @var KeyedCollection $controls */
        // If collections aren't built this way, their factory won't be able to build custom types
        $controls = $this->getFormFactory()->getFormElementManager()->create('KeyedCollection');
        $controls->setName('controls');
        $controls->setLabel('Controls');
        $controls->setOption('delimiter', ' | ');

        $controls->add(
            array(
                'type' => 'link',
                'name' => 'edit',
                'options' => array(
                    'label' => 'Edit',
                    'idLinkRoute' => 'zfcadmin/zfcuseradmin/edit',
                    'idLinkParam' => 'userId',
                ),
            )
        );

        $controls->add(
            array(
                'type' => 'link',
                'name' => 'delete',
                'options' => array(
                    'label' => 'Delete',
                    'idLinkRoute' => 'zfcadmin/zfcuseradmin/remove',
                    'idLinkParam' => 'userId',
                ),
                'attributes' => array(
                    'onclick' => 'return confirm(\'Really delete user?\')'
                )
            )
        );

        $this->add(
            $controls,
            array(
                // end of table
                'priority' => -100,
            )
        );

    }

    public function setUserListOptions(UserListOptionsInterface $userListOptions)
    {
        $this->userListOptions = $userListOptions;
        return $this;
    }

    /**
     * @return UserListOptionsInterface | null
     */
    public function getUserListOptions()
    {
        return $this->userListOptions;
    }
}