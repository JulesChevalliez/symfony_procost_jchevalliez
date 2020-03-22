<?php
/**
 * Created by PhpStorm.
 * User: jules
 * Date: 15/02/2020
 * Time: 10:22
 */

namespace App\Controller;

use App\Entity\Jobs;
use App\Entity\User;
use App\Repository\ProductionsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use function MongoDB\BSON\toJSON;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Repository\UserRepository;
use App\Repository\JobsRepository;
use App\Repository\ProjectsRepository;


class MainController extends AbstractController
{

    private $em;
    private $usersRepository;
    private $jobsRepository;
    private $projectsRepository;
    private $productionsRepository;

    public function __construct(
        EntityManagerInterface $em,
        UserRepository $usersRepository,
        JobsRepository $jobsRepository,
        ProjectsRepository $projectsRepository,
        ProductionsRepository $productionsRepository
    )
    {
        $this->em = $em;
        $this->usersRepository = $usersRepository;
        $this->jobsRepository = $jobsRepository;
        $this->projectsRepository = $projectsRepository;
        $this->productionsRepository = $productionsRepository;
    }

    /**
     * @Route("/", name="main_homepage")
     */
    public function homepage(): Response
    {
        $currentUser = $this->getUser();
//        sizeof($currentUser->getJob());
//        dd(json_encode($currentUser->getJob()));
        $nbrFinished = $this->projectsRepository->getNbrFinished();
        $projects = $this->projectsRepository->getLastProjects();
        $productions = $this->productionsRepository->getLastProds();
        $nbrProd = $this->productionsRepository->getNbrHourProd();
        $employees = $this->usersRepository->countEmployee();

        $totalProject = $nbrFinished[0]['inProgress'] + $nbrFinished[0]['finished'];
        $tauxDelivered = ($nbrFinished[0]["finished"] / $totalProject) * 100;
        $restDelivered = 100 - $tauxDelivered;
//
        $worthProjects = $this->projectsRepository->getNbrWorthProject();
        $tauxWorthProject = ($worthProjects[0]["nbrWorthProject"] / $totalProject) * 100;
        $restWorthProject = 100 - $tauxWorthProject;

        $bestEmployee = $this->productionsRepository->getBestEmployee();

        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException();
        }
        return $this->render('index.html.twig', [
            "nbrFinished" => $nbrFinished[0],
            "projects" => $projects,
            "productions" => $productions,
            "employee" => $employees[0],
            "nbrProd" => $nbrProd[0],
            "tauxDelivered" => $tauxDelivered,
            "resteDelivered" => $restDelivered,
            "tauxWorthProject" => $tauxWorthProject,
            "restWorthProject" => $restWorthProject,
            "bestEmployee" => $bestEmployee[0],
            "currentUser" => $currentUser

        ]);
    }

    /**
     * @Route("/users", name="main_users")
     */
    public function users(PaginatorInterface $paginator, Request $request): Response
    {
        $currentUser = $this->getUser();
        $users = $paginator->paginate(
            $this->usersRepository->getAllWithJob(),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('pages/users.html.twig', [
            "users" => $users,
            "currentUser" => $currentUser
        ]);
    }


    /**
     * @Route("/projects", name="main_projects")
     */
    public function projects(PaginatorInterface $paginator, Request $request): Response
    {
        $currentUser = $this->getUser();
        $projects = $paginator->paginate(
            $this->projectsRepository->findAllQuery(),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('pages/projects.html.twig', [
            'projects' => $projects,
            "currentUser" => $currentUser
        ]);
    }

    /**
     * @Route("/jobs", name="main_jobs")
     */
    public function jobs(PaginatorInterface $paginator, Request $request): Response
    {

        //Je sais pas pour qu'elle foutu raison il faut ça mais sinon on peu pas récup la collection d'user.
        $tests = $this->jobsRepository->findAll();
        foreach ($tests as $test){
            sizeof($test->getUsers());
        }

        $currentUser = $this->getUser();
        $jobs = $paginator->paginate(
            $this->jobsRepository->findAllQuery(),
            $request->query->getInt('page', 1),
            5
        );
        return $this->render('pages/jobs.html.twig', [
            "jobs" => $jobs,
            "currentUser" => $currentUser
        ]);
    }


}