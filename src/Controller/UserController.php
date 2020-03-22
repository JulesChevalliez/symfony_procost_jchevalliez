<?php
/**
 * Created by PhpStorm.
 * User: jules
 * Date: 01/03/2020
 * Time: 10:49
 */

namespace App\Controller;

use App\Entity\Jobs;
use App\Entity\Productions;
use App\Entity\Projects;
use App\Entity\User;
use App\Form\AddUserType;
use App\Form\ProductionType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Repository\JobsRepository;
use App\Repository\ProjectsRepository;

class UserController extends AbstractController
{

    private $em;
    private $usersRepository;
    private $jobsRepository;
    private $projectsRepository;
    private $encoder;

    public function __construct(
        EntityManagerInterface $em,
        UserRepository $usersRepository,
        JobsRepository $jobsRepository,
        ProjectsRepository $projectsRepository,
        UserPasswordEncoderInterface $encoder
    )
    {
        $this->em = $em;
        $this->usersRepository = $usersRepository;
        $this->jobsRepository = $jobsRepository;
        $this->projectsRepository = $projectsRepository;
        $this->encoder = $encoder;
    }

    /**
     * @Route("/details/{id}", name="user_details")
     */
    public function detailsUser(Request $request, int $id, PaginatorInterface $paginator): Response
    {
        $currentUser = $this->getUser();

        $user = $this->usersRepository->getDetailsUser($id);
        $userProd = $user[0]->getProductions();
        $userProd = $paginator->paginate(
            $userProd,
            $request->query->getInt('page', 1),
            5
        );
        $prod = new Productions();
        $formProd = $this->createForm(ProductionType::class, $prod);
        $formProd->handleRequest($request);
        if ($formProd->isSubmitted() && $formProd->isValid()) {
            $req = $formProd->getData();
            $req->setUser($user[0])
                ->setCreationDate(new \DateTime())
                ->setTotalCost($req->getHourNumber() * $req->getUser()->getCost());
            $project = $this->projectsRepository->find($req->getProject()->getId());
            $project->setTotalCost($project->getTotalCost() + $req->getTotalCost());

            $this->em->persist($project);
            $this->em->flush();
            $this->em->persist($req);
            $this->em->flush();
            $this->addFlash('succes', 'Temp de travail ajouté !');
            return $this->redirectToRoute('user_details', ["id" => $id]);
        }

        $projects = $this->projectsRepository->findAll();
        return $this->render('pages/users/details_user.html.twig', [
            "user" => $user[0],
            "projects" => $projects,
            "formProd" => $formProd->createView(),
            "userProd" => $userProd,
            "currentUser" => $currentUser
        ]);
    }

    /**
     * @Route("/edit/user/{id}", name="user_edit")
     */
    public function userForm(Request $request, int $id): Response
    {
        $currentUser = $this->getUser();

        $usr = $this->usersRepository->getOneWithJob($id);
        $form = $this->createForm(UserType::class, $usr);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $req = $form->getData();
            $req[0]->setName($req['name'])
                ->setFirstName($req['firstName'])
                ->setEmail($req['email'])
                ->setCost($req['cout'])
                ->setJob($req['job'])
                ->setHiringDate($req['date']);
            $this->em->persist($req[0]);
            $this->em->flush();
            $this->addFlash('succes', 'Vos modifications ont bien été prises en compte !');
            return $this->redirectToRoute('user_details', ["id" => $id]);
        }
        return $this->render('pages/users/edit_user.html.twig', [
            "form" => $form->createView(),
            "currentUser" => $currentUser
        ]);
    }

    /**
     * @Route("/addUser", name="user_add")
     */
    public function addUser(Request $request): Response
    {
        $currentUser = $this->getUser();

        $usr = new User();
        $form = $this->createForm(AddUserType::class, $usr);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $req = $form->getData();
            $password = $req->getPassword();
            $password = $this->encoder->encodePassword($usr, $password);
            $req->setRoles(['ROLE_EMPLOYEE'])
                ->setHiringDate(new \DateTime())
                ->setPassword($password);
            $this->em->persist($req);
            $this->em->flush();
            $this->addFlash('succes', 'Employé ajouter !');
            return $this->redirectToRoute('main_users');
        }
        return $this->render('pages/users/add_user.html.twig', [
            "form" => $form->createView(),
            "currentUser" => $currentUser
        ]);
    }

}