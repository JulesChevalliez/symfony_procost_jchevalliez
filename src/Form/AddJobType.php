<?php
/**
 * Created by PhpStorm.
 * User: jules
 * Date: 10/03/2020
 * Time: 09:24
 */

namespace App\Form;


use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AddJobType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', TextType::class, [
                'label' => 'Libellé',
                'required' => true
            ]);
    }
}