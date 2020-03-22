<?php
/**
 * Created by PhpStorm.
 * User: jules
 * Date: 09/03/2020
 * Time: 08:39
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;


class EditProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user =  $options["data"];
//        dd($user);

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du projet',
                'required' => true,
                'data' => $user->getName()
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => true,
                'data' => $user->getDescription()
            ])
            ->add('selling_price', IntegerType::class, [
                'label' => 'Prix de vente (en €)',
                'required' => true,
                'attr' => array('min' => 0),
                'data' => $user->getSellingPrice()
            ])
            ->add('delivered', CheckboxType::class, [
                'label' => 'Project terminer ?',
                'required' => false

            ]);
    }
}