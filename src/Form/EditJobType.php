<?php
/**
 * Created by PhpStorm.
 * User: jules
 * Date: 14/03/2020
 * Time: 08:08
 */

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EditJobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $job =  $options["data"];

        $builder
            ->add('name', TextType::class, [
                'label' => 'Libellé',
                'required' => true,
                'data' => $job->getName()
            ]);
    }
}