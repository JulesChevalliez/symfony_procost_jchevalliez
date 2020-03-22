<?php
/**
 * Created by PhpStorm.
 * User: jules
 * Date: 08/03/2020
 * Time: 08:26
 */

namespace App\Form;

use App\Entity\Jobs;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;

class AddUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'required' => true
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => true
            ])
            ->add('cost', IntegerType::class, [
                'label' => 'Coût horraire (en €)',
                'required' => true
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => true
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne sont pas identique.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Confimer mot de passe'],
            ])
//            ->add('roles', ChoiceType::class, [
//                'label' => 'Rôle',
//                'required' => true,
//                'multiple' => true,
//                'expanded' => true,
//                'choices' => [
//                    'Manager' => "ROLE_MANAGER",
//                    'Employé' => "ROLE_EMPLOYEE"
//                ]
//            ])
            ->add('job', EntityType::class, [
                "class" => Jobs::class,
                "choice_label" => "name"
            ]);
    }
}