<?php

namespace Api\Form;
use Zend\Form\Form;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use Api\Validator\SsoEmailExistsValidator;
use Api\Validator\SsoUsernameExistsValidator;

#use Admin\Validator\UserExistsValidator;
/**
 * This form is used to collect user's email, full name, password and status. The form 
 * can work in two scenarios - 'create' and 'update'. In 'create' scenario, user
 * enters password, in 'update' scenario he/she doesn't enter password.
 */

class ApiVerifyMobileForm extends Form {

    /**
     * Scenario ('create' or 'update').
     * @var string 
     */
    private $scenario;

    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager 
     */
    private $entityManager = null;

    /**
     * Current user.
     * @var Common\Entity\Userss
     */
    private $user = null;
    private $required = true;

    /**
     * Constructor.     
     */
    public function __construct($scenario = 'api', $entityManager = null, $user = null) {
        // Define form name
        parent::__construct('user-sso-form');

        // Set POST method for this form
        $this->setAttribute('method', 'post');

        // Save parameters for internal use.
        $this->scenario = $scenario;
        $this->entityManager = $entityManager;
        $this->user = $user;

        $this->addElements();
        $this->addInputFilter();
       
    }

    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements() {
        
       // Add "password" field
        $this->add([
                'type' => 'text',
                'name' => 'mobile',
                'options' => [
                    'label' => 'Mobile',
                ],
        ]);
        
        // Add "full_name" field
        $this->add([
            'type' => 'text',
            'name' => 'username',
            'options' => [
                'label' => 'User Name',
            ],
        ]);

        // Add the Submit button
        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Create'
            ],
        ]);
    }

    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter() {
        // Create main input filter
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);
        // Add input for "full_name" field
            
            $inputFilter->add([
                'name' => 'mobile',
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 10,
                            'max' => 13
                        ],
                    ],
                ],
            ]);
            
            
            
            
        $inputFilter->add([
            'name' => 'username',
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 1,
                        'max' => 512
                    ],
                ],
            ],
        ]);
    }

}
