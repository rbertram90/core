<?php

use rbwebdesigns\core\Form;

class myForm extends Form {

    public function saveData() {
        // Required implementation of abstract method.
    }
    
}

// beforeEach(function() {
//   
// });

test('has created a form id', function () {
    $form = new myForm();

    expect($form->id())->toBeString();
});
