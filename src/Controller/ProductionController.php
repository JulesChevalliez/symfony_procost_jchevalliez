<?php
/**
 * Created by PhpStorm.
 * User: jules
 * Date: 06/03/2020
 * Time: 11:21
 */

namespace App\Controller;


use App\Entity\Productions;
use App\Entity\Projects;
use App\Form\ProductionType;
use App\Repository\ProductionsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductionController extends AbstractController
{

    private $em;
    private $productionsRepository;

    public function __construct(
        EntityManagerInterface $em,
        ProductionsRepository $productionsRepository
    )
    {
        $this->em = $em;
        $this->productionsRepository = $productionsRepository;
    }

}