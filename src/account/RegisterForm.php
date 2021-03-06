<?php
namespace rbwebdesigns\core\account;

use rbwebdesigns\core\Form;

abstract class RegisterForm extends Form
{
    public function __construct()
    {
        parent::__construct();

        // Provide helpful defaults for a generic login form
        $this->action = '/account/register';
        $this->method = 'POST';
        $this->submitLabel = 'Register';
        $this->fields = [
            'username' => [
                'before' => '',
                'after' => '',
                'label' => 'Username',
                'type' => 'text',
                'required' => true
            ],
            'email' => [
                'before' => '',
                'after' => '',
                'label' => 'Email address',
                'type' => 'text',
                'required' => true
            ],
            'password' => [
                'before' => '',
                'after' => '',
                'label' => 'Password',
                'type' => 'password',
                'required' => true
            ],
            'password_confirm' => [
                'before' => '',
                'after' => '',
                'label' => 'Confirm password',
                'type' => 'password',
                'required' => true
            ]
        ];
        $this->actions = [
            [
                'type' => 'submit',
                'label' => 'Register',
            ]
        ];
        $this->setAttribute('id', 'form_login');
    }

    public function validate()
    {
        $username = $this->fields['username']['value'];
        $password = $this->fields['password']['value'];
        $password_confirm = $this->fields['password_confirm']['value'];
        
        if (strlen($username) < 3) {
            $this->error = "Username must be more that 3 characters";
            return false;
        }

        if ($password !== $password_confirm) {
            $this->error = "Password fields do not match";
            return false;
        }

        return true;
    }

}