<?php

namespace Api\Form;

use Zend\Form\Form;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use Api\Validator\SsoEmailExistsValidatorForThirdParty;


#use Admin\Validator\UserExistsValidator;
/**
 * This form is used to collect user's email, full name, password and status. The form 
 * can work in two scenarios - 'create' and 'update'. In 'create' scenario, user
 * enters password, in 'update' scenario he/she doesn't enter password.
 */

class ApiThirdpartySsoForm extends Form {

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
    public function __construct($scenario = 'create', $entityManager = null, $user = null) {
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
        // Add "email" field
        $this->add([
            'type' => 'text',
            'name' => 'ssoEmail',
            'options' => [
                'label' => 'E-mail',
            ],
        ]);
       // Add "password" field
        $this->add([
                'type' => 'password',
                'name' => 'ssoPassword',
                'options' => [
                    'label' => 'Password',
                ],
        ]);

            $this->add([
                'type' => 'text',
                'name' => 'username',
                'options' => [
                    'label' => 'User Name',
                ],
            ]);
        // Add "full_name" field
        $this->add([
            'type' => 'text',
            'name' => 'firstname',
            'options' => [
                'label' => 'First Name',
            ],
        ]);
        // Add "full_name" field
        $this->add([
            'type' => 'text',
            'name' => 'surname',
            'options' => [
                'label' => 'Last Name',
            ],
        ]);
        // Add "full_name" field
        $this->add([
            'type' => 'date',
            'name' => 'dob',
            'options' => [
                'label' => 'Date Of Birth',
                'step' => 'any'
            ],
        ]);
        // Add "full_name" field
        $this->add([
            'type' => 'text',
            'name' => 'mobile',
            'options' => [
                'label' => 'Mobile Number',
            ],
        ]);
        // Add "full_name" field
        $this->add([
            'type' => 'text',
            'name' => 'address',
            'options' => [
                'label' => 'Address',
            ],
        ]);
        // Add "full_name" field
        $this->add([
            'type' => 'text',
            'name' => 'suburb',
            'options' => [
                'label' => 'Suburb',
            ],
        ]);
        // Add "full_name" field
        $this->add([
            'type' => 'text',
            'name' => 'state',
            'options' => [
                'label' => 'State',
            ],
        ]);
        // Add "full_name" field
        $this->add([
            'type' => 'text',
            'name' => 'postcode',
            'options' => [
                'label' => 'Postcode',
            ],
        ]);

        $this->add([
            'type' => 'select',
            'name' => 'gender',
            'options' => [
                'label' => 'Gender',
                'value_options' => [
                    'Male' => 'Male',
                    'Female' => 'Female',
                    'male' => 'male',
                    'female' => 'female',
                ]
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
                'name' => 'ssoEmail',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 128
                        ],
                    ],
                    [
                        'name' => 'EmailAddress',
                        'options' => [
                            'allow' => \Zend\Validator\Hostname::ALLOW_DNS,
                            'useMxCheck' => false,
                        ],
                    ],
                    [
                        'name' => SsoEmailExistsValidatorForThirdParty::class,
                        'options' => [
                            'entityManager' => $this->entityManager,
                            'user' => $this->user,
                            'update'=>1
                        ],
                    ],
                ],
            ]);

                $inputFilter->add([
                    'name' => 'username',
                    'required' => false, 'allow_empty' => true,
                    'filters' => [
                        ['name' => 'StringTrim'],
                    ],
                    'validators' => [
                        [
                            'name' => 'StringLength',
                            'options' => [
                                'min' => 1,
                                'max' => 120
                            ],
                        ]
                    ],
                ]);

                $inputFilter->add([
                    'name' => 'ssoPassword',
                    'required' => false, 'allow_empty' => true,
                    'filters' => [
                        ['name' => 'StringTrim'],
                    ],
                    'validators' => [
                        [
                            'name' => 'StringLength',
                            'options' => [
                                'min' => 6,
                                'max' => 50
                            ],
                        ],
                    ],
                ]);


            $inputFilter->add([
                'name' => 'firstname',
                'required' => false,
                'allow_empty' => true,
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

            $inputFilter->add([
                'name' => 'gender',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 6
                        ],
                    ],
                ],
            ]);
        $inputFilter->add([
            'name' => 'surname',
            'required' => false, 'allow_empty' => true,
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
        $inputFilter->add([
            'name' => 'mobile',
            'required' => false, 'allow_empty' => true,
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
        $inputFilter->add([
            'name' => 'address',
            'required' => false, 'allow_empty' => true,
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

        $inputFilter->add([
            'name' => 'state',
            'required' => false, 'allow_empty' => true,
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

        $inputFilter->add([
            'name' => 'postcode',
            'required' => false, 'allow_empty' => true,
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
        $inputFilter->add([
            'name' => 'suburb',
            'required' => false,
            'allow_empty' => true,
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
            $inputFilter->add([
                'name' => 'dob',
                'required' => false, 'allow_empty' => true,
            ]);

    }

}