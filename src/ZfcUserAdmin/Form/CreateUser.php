<?php

namespace ZfcUserAdmin\Form;

use ZfcUser\Options\RegistrationOptionsInterface;
use ZfcUser\Form\Register as Register;
use ZfcUserAdmin\Options\UserCreateOptionsInterface;

class CreateUser extends Register
{
    /**
     * @var UserCreateOptionsInterface
     */
    protected $createOptions;

    protected $serviceManager;

    public function __construct($name = null, UserCreateOptionsInterface $createOptions, RegistrationOptionsInterface $registerOptions, $serviceManager)
    {
        $this->setCreateOptions($createOptions);
        $this->setServiceManager($serviceManager);
        parent::__construct($name, $registerOptions);
    }

    public function init()
    {
        parent::init();

        if ($this->getCreateOptions()->getCreateUserAutoPassword()) {
            $this->remove('password');
            $this->remove('passwordVerify');
        }

        foreach ($this->getCreateOptions()->getCreateFormElements() as $name => $element) {
            // avoid adding fields twice (e.g. email)
            // if ($this->get($element)) continue;

            $this->add(array(
                'name' => $element,
                'options' => array(
                    'label' => $name,
                ),
                'attributes' => array(
                    'type' => 'text'
                ),
            ));
        }

        $this->get('submit')->setLabel('Create');
    }

    public function setCreateOptions(UserCreateOptionsInterface $createOptionsOptions)
    {
        $this->createOptions = $createOptionsOptions;
        return $this;
    }

    public function getCreateOptions()
    {
        return $this->createOptions;
    }

    public function setServiceManager($serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Override isValid() to set an validation group of all elements that do not
     * have an 'exclude' option, if at least one element has this option set.
     *
     * @return boolean
     */
    public function isValid()
    {
        if ($this->hasValidated) {
            return $this->isValid;
        }

        if ($this->getValidationGroup() === null) {
            // Add all non-excluded elements to the validation group
            $validationGroup = null;
            foreach ($this->getElements() as $element) {
                if ($element->getOption('exclude') === false or
                    ($element->getAttribute('readonly') !== true && $element->getOption('exclude') !== true))
                {
                    $validationGroup[] = $element->getName();
                }
            }
            if ($validationGroup) {
                $this->setValidationGroup($validationGroup);
            }
        }

        return parent::isValid();
    }
}
