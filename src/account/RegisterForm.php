<?php
namespace rbwebdesigns\core\account;

use rbwebdesigns\core\form\Form;

abstract class RegisterForm extends Form
{
    public string $action = '/account/register';
    public string $method = 'POST';
    public array $fields = [
        'username' => [
            'label' => 'Username',
            'type' => 'text',
            'required' => true
        ],
        'email' => [
            'label' => 'Email address',
            'type' => 'text',
            'required' => true
        ],
        'password' => [
            'label' => 'Password',
            'type' => 'password',
            'required' => true
        ],
        'password_confirm' => [
            'label' => 'Confirm password',
            'type' => 'password',
            'required' => true
        ]
    ];
    public array $actions = [
        [
            'type' => 'submit',
            'label' => 'Register',
        ]
    ];

    public function validate(): bool
    {
        $username = $this->fields['username']['value'];
        $password = $this->fields['password']['value'];
        $password_confirm = $this->fields['password_confirm']['value'];
        
        if (strlen($username) < 3) {
            $this->validationErrors['username'] = "Username must be more than 3 characters";
            return false;
        }

        if ($password !== $password_confirm) {
            $this->validationErrors['password_confirm'] = "Password fields do not match";
            return false;
        }

        return true;
    }

}