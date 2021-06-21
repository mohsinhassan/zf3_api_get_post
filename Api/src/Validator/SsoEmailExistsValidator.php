<?php

namespace Api\Validator;

use Zend\Validator\AbstractValidator;
use Common\Entity\Usersso;

/**
 * This validator class is designed for checking if there is an existing user 
 * with such an email.
 */
class SsoEmailExistsValidator extends AbstractValidator {

    /**
     * Available validator options.
     * @var array
     */
    protected $options = array(
        'entityManager' => null,
        'user' => null,
        'update' => null
    );

    // Validation failure message IDs.
    const NOT_SCALAR = 'notScalar';
    const USER_EXISTS = 'userExists';
    const EmailTaken = 'This Email is already taken.';    

    /**
     * Validation failure messages.
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_SCALAR => "The email must be a scalar value",
        self::USER_EXISTS => "Another user with such an email already exists"
    );

    /**
     * Constructor.     
     */
    public function __construct($options = null) {
        // Set filter options (if provided).
        if (is_array($options)) {
            if (isset($options['entityManager']))
                $this->options['entityManager'] = $options['entityManager'];
            if (isset($options['user']))
                $this->options['user'] = $options['user'];
            if (isset($options['update']))
                $this->options['update'] = $options['update'];
        }

        // Call the parent class constructor
        parent::__construct($options);
    }

    /**
     * Check if user exists.
     */
    public function isValid($value) {

        if (!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }
        // Get Doctrine entity manager.
        $entityManager = $this->options['entityManager'];
        if ($this->options['update'] != 1) {
            $user = $entityManager->getRepository(Usersso::class)
                    ->findOneByEmail($value);
            if (!empty($user)) {
                $isValid = false;
            } else {
                $isValid = true;
            }

            // If there were an error, set error message.
            if (!$isValid) {
                $this->error(self::USER_EXISTS);
            }

            // Return validation result.
            return $isValid;
        } else {

            $user = $entityManager->getRepository(Usersso::class)
                    ->findOneByuserName($this->options['user']['username']);
            if (!empty($user)) {
                $isValid = false;

                if ($user->getEmail() == $this->options['user']['ssoEmail']) {
                    $isValid = true;
                } else {

                    $user = $entityManager->getRepository(Usersso::class)
                            ->findOneByEmail($value);
                    if (!empty($user)) {
                        $isValid = false;
                    } else {
                        $isValid = true;
                    }
                }
            }
            if (!$isValid) {
                $this->error(self::EmailTaken);
            }            
            return $isValid;
        }
    }

}
