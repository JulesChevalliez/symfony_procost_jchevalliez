<?php
/**
 * Created by PhpStorm.
 * User: jules
 * Date: 06/03/2020
 * Time: 11:39
 */

namespace App\Form;

use App\Entity\Productions;
use App\Repository\ProjectsRepository;
use Symfony\Component\Form\AbstractType;
use App\Entity\Projects;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        dd($options);
        $builder
            ->add('hour_number', IntegerType::class, [
                'label' => 'Nombre d\'heures',
                'required' => true,
                'attr' => array('min' => 0)
            ])
            ->add('project', EntityType::class, [
                "choice_label" => "name",
                "class" => Projects::class,
                "query_builder" => function (ProjectsRepository $projectsRepository) {
                    return $projectsRepository->createQueryBuilder('pr')
                        ->where('pr.delivered = 0');
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Productions::class,
        ]);
    }
}