<?php
/**
 * Created by PhpStorm.
 * User: jules
 * Date: 08/03/2020
 * Time: 10:43
 */

namespace App\Controller;

use App\Entity\Projects;
use App\Form\AddProjectType;
use App\Form\EditProjectType;
use App\Repository\ProductionsRepository;
use App\Repository\ProjectsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends AbstractController
{

    private $em;
    private $projectsRepository;
    private $productionsRepository;

    public function __construct(
        EntityManagerInterface $em,
        ProjectsRepository $projectsRepository,
        ProductionsRepository $productionsRepository
    )
    {
        $this->em = $em;
        $this->projectsRepository = $projectsRepository;
        $this->productionsRepository = $productionsRepository;
    }

    /**
     * @Route("/detail/project/{id}", name="project_detail")
     */
    public function detailsProject(Request $request, int $id, PaginatorInterface $paginator): Response
    {
        $currentUser = $this->getUser();
        $project = $this->projectsRepository->getProjectWithNbrEmployee($id);
        $listEmployees = $this->productionsRepository->getAllEmployee($id);
        $listEmployees = $paginator->paginate(
            $listEmployees,
            $request->query->getInt('page', 1),
            5
        );
        return $this->render('pages/projects/details_project.html.twig', [
            'project' => $project[0],
            'listEmployees' => $listEmployees,
            "currentUser" => $currentUser
        ]);
    }

    /**
     * @Route("/add/project", name="project_add")
     */
    public function addProject(Request $request): Response
    {
        $currentUser = $this->getUser();
        $project = new Projects();
        $form = $this->createForm(AddProjectType::class, $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $req = $form->getData();
            $req->setCreationDate(new \DateTime())
                ->setDelivered(false);
            $this->em->persist($req);
            $this->em->flush();
            $this->addFlash('succes', 'Projet ajouter !');
            return $this->redirectToRoute('main_projects');

        }
        return $this->render('pages/projects/add_project.html.twig', [
            "formAdd" => $form->createView(),
            "currentUser" => $currentUser
        ]);
    }

    /**
     * @return Response
     * @Route("/edit/project/{id}", name="project_edit")
     */
    public function editProject(Request $request, int $id): Response
    {

        /*Recupère tout les projets terminer, pour rediriger si l'id dans l'url correspond*/
        $finishedProjects = $this->projectsRepository->getAllFinishedProject();
        foreach ($finishedProjects as $finishedProject)
        {
            if ($finishedProject->getId() == $id) {
                return $this->redirectToRoute('main_projects');
            }
        }

        $currentUser = $this->getUser();
        $project = $this->projectsRepository->find($id);
        $form = $this->createForm(EditProjectType::class, $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $req = $form->getData();
            if ($req->getDelivered() == true)
            {
                $req->setDeliveryDate(new \DateTime());
            }
            $this->em->persist($req);
            $this->em->flush();
            $this->addFlash('succes', 'Projet modifier !');
            return $this->redirectToRoute('project_detail', ["id" => $id]);
        }
        return $this->render('pages/projects/edit_project.html.twig', [
            "form" => $form->createView(),
            "currentUser" => $currentUser
        ]);
    }
}