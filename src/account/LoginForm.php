<?php
namespace rbwebdesigns\core\account;

use rbwebdesigns\core\Form;

/**
 * class LoginForm
 * 
 * Provides helpful defaults and a good starting point
 * for a login form.
 * 
 * Submit method will need to be implemented on a site
 * by site basis.
 * 
 * @author R Bertram <ricky@rbwebdesigns.co.uk>
 */
abstract class LoginForm extends Form
{
    public function __construct()
    {
        parent::__construct();

        $this->action = '/account/login';
        $this->method = 'POST';
        $this->submitLabel = 'Log in';
        $this->fields = [
            'username' => [
                'before' => '',
                'after' => '',
                'label' => 'Username',
                'type' => 'text',
                'required' => true
            ],
            'password' => [
                'before' => '',
                'after' => '',
                'label' => 'Password',
                'type' => 'password',
                'required' => true
            ]
        ];
        $this->actions = [
            [
                'type' => 'submit',
                'label' => 'Log in',
            ]
        ];
        $this->setAttribute('id', 'form_login');
    }

}