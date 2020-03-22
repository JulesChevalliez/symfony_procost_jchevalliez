<?php
/**
 * Created by PhpStorm.
 * User: jules
 * Date: 01/03/2020
 * Time: 11:13
 */

namespace App\Form;

use App\Entity\Jobs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;


class UserType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user =  $options["data"][0];

        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
                'data' => $user->getFirstName()
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'data' => $user->getName()
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
                'data' => $user->getEmail()
            ])
            ->add('cout', IntegerType::class, [
                'label' => 'Coût horaire (en €)',
                'required' => true,
                'attr' => array('min' => 0),
                'data' => $user->getCost()
            ])
            ->add('job', EntityType::class, [
                "class" => Jobs::class,
                "choice_label" => "name"
            ])
            ->add('date', DateType::class, [
                'label' => 'Date d\'embauche',
                'required' => true,
                'data' => $user->getHiringDate()
            ])
        ;
    }

}