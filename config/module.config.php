<?php

return array(
    'view_manager' => array(
        'template_path_stack' => array(
            'zfcuseradmin'      => __DIR__ . '/../view',
        ),
    ),

    'form_elements' => array(
        'invokables' => array(
            'keyedcollection'   => 'DaleyTable\KeyedCollection',
            'table'             => 'DaleyTable\Table',
            'link'              => 'DaleyTable\Element\Link',
        ),
        'factories' => array(
            'zfcuseradmin_createuser_form'      => 'ZfcUserAdmin\FormElementManagerFactory\Form\CreateUser',
            'zfcuseradmin_edituser_form'        => 'ZfcUserAdmin\FormElementManagerFactory\Form\EditUser',
            'ZfcUserAdmin\Table\UserList'       => 'ZfcUserAdmin\FormElementManagerFactory\Table\UserList',
        )
    ),
    'view_helpers' => array(
        'invokables' => array(
            'formlink'          => 'DaleyTable\View\Helper\FormLink',
            'formspan'          => 'DaleyTable\View\Helper\FormSpan',
            'table'             => 'DaleyTable\View\Helper\Table',
            'tableHeaderCell'   => 'DaleyTable\View\Helper\TableHeaderCell',
            'tableHeaderRow'    => 'DaleyTable\View\Helper\TableHeaderRow',
            'tableStaticCell'   => 'DaleyTable\View\Helper\TableStaticCell',
            'tableStaticRow'    => 'DaleyTable\View\Helper\TableStaticRow',
        ),
        'delegators' => array(
            'form_element' => array(
                'ZfcUserAdmin\Delegator\FormElementLinkSupport',
            ),
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'zfcuseradmin'      => 'ZfcUserAdmin\Controller\UserAdminController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'zfcadmin' => array(
                'child_routes' => array(
                    'zfcuseradmin' => array(
                        'type' => 'Literal',
                        'priority' => 1000,
                        'options' => array(
                            'route' => '/user',
                            'defaults' => array(
                                'controller' => 'zfcuseradmin',
                                'action'     => 'index',
                            ),
                        ),
                        'child_routes' =>array(
                            'list' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/list[/:p]',
                                    'defaults' => array(
                                        'controller' => 'zfcuseradmin',
                                        'action'     => 'list',
                                    ),
                                ),
                            ),
                            'create' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/create',
                                    'defaults' => array(
                                        'controller' => 'zfcuseradmin',
                                        'action'     => 'create'
                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/edit/:userId',
                                    'defaults' => array(
                                        'controller' => 'zfcuseradmin',
                                        'action'     => 'edit',
                                        'userId'     => 0
                                    ),
                                ),
                            ),
                            'remove' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/remove/:userId',
                                    'defaults' => array(
                                        'controller' => 'zfcuseradmin',
                                        'action'     => 'remove',
                                        'userId'     => 0
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),

    'navigation' => array(
        'admin' => array(
            'zfcuseradmin' => array(
                'label' => 'Users',
                'route' => 'zfcadmin/zfcuseradmin/list',
                'pages' => array(
                    'create' => array(
                        'label' => 'New User',
                        'route' => 'zfcadmin/zfcuseradmin/create',
                    ),
                ),
            ),
        ),
    ),

    'zfcuseradmin' => array(
        'zfcuseradmin_mapper' => 'ZfcUserAdmin\Mapper\UserZendDb',
    )
);
