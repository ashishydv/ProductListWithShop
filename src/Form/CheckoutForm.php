<?php
namespace App\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class CheckoutForm extends Form
{
    protected function _buildSchema(Schema $schema)
    {
        return $schema->addField('name', 'string')
            ->addField('email', ['type' => 'string']);
    }

    protected function _buildValidator(Validator $validator)
    {

        $validator
            ->notEmpty('name')
            ->add('name',[
            'maxLength' => [
                'rule' => ['maxLength',30],
                'message' => 'Max length 30 allowed'
            ]
            ]);

      $validator->add('email', 'format', [
                'rule' => 'email',
                'message' => 'A valid email address is required',
            ])
            ->notEmpty('email');
    }

    protected function _execute(array $data)
    {
        return true;
    }
}
?>
