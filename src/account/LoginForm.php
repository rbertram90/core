<?php
namespace rbwebdesigns\core\account;

use rbwebdesigns\core\form\Form;

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
    public string $action = '/account/login';
    public string $method = 'POST';
    public array $fields = [
        'username' => [
            'label' => 'Username',
            'type' => 'text',
            'required' => true
        ],
        'password' => [
            'label' => 'Password',
            'type' => 'password',
            'required' => true
        ]
    ];
    public array $actions = [
        [
            'type' => 'submit',
            'label' => 'Log in',
        ]
    ];
}
