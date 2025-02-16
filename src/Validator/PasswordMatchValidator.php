<?php
//src/Validator/PasswordMatch.php
namespace src\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Form\FormInterface;



/**
 * @Annotation
 */
class PasswordMatch extends Constraint
{
    public $message = 'The passwords do not match.';
}

class PasswordMatchValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        // Assuming the context is a form and comparing password and confirm_password
        $form = $this->context->getObject();
        
        // Check if both fields are present in the form
        if ($form instanceof FormInterface) {
            $password = $form->get('password')->getData();
            $confirmPassword = $form->get('confirm_password')->getData();
            
            // Validate that passwords match
            if ($password !== $confirmPassword) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('confirm_password')  // Set the error on the 'confirm_password' field
                    ->addViolation();
            }
        }
    }
}