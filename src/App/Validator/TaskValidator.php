<?php

namespace App\Validator;

use Framework\Security\Model\AuthManager;

/**
 * Class TaskValidator
 */
class TaskValidator
{
    /**
     * @var AuthManager
     */
    protected $authManager;

    /**
     * TaskValidator constructor.
     * @param AuthManager $authManager
     */
    public function __construct(AuthManager $authManager)
    {
        $this->authManager = $authManager;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function validate($data)
    {
        $errors = [];

        if (empty($data['username'])) {
            $errors['Username should not be empty.'];
        }

        if (empty($data['email'])) {
            $errors['Email should not be empty'];
        } else if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['Email should be a valid email value.'];
        }

        if (isset($data['status'])) {
            $errors[] = 'Status can not be added along with other task data.';
        }

        if (!$this->authManager->isLoggedIn() && isset($data['content']) && !empty($data['id'])) {
            $errors[] = 'Content can only be edited by authorized user.';
        }

        return $errors;
    }
}