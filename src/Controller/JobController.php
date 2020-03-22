<?php
/**
 * Created by PhpStorm.
 * User: jules
 * Date: 14/03/2020
 * Time: 08:05
 */

namespace App\Controller;


use App\Entity\Jobs;
use App\Form\AddJobType;
use App\Form\EditJobType;
use App\Repository\JobsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class JobController extends AbstractController
{
    private $em;
    private $usersRepository;
    private $jobsRepository;
    private $projectsRepository;
    private $encoder;

    public function __construct(
        EntityManagerInterface $em,
//        UserRepository $usersRepository,
        JobsRepository $jobsRepository
//        ProjectsRepository $projectsRepository,
//        UserPasswordEncoderInterface $encoder
    )
    {
        $this->em = $em;
//        $this->usersRepository = $usersRepository;
        $this->jobsRepository = $jobsRepository;
//        $this->projectsRepository = $projectsRepository;
//        $this->encoder = $encoder;
    }


    /**
     * @Route("/edit/job/{id}", name="job_edit")
     */
    public function editJobs(Request $request, int $id): Response
    {
        $currentUser = $this->getUser();
        $job = $this->jobsRepository->find($id);
        $form = $this->createForm(EditJobType::class, $job);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $req = $form->getData();
            $this->em->persist($req);
            $this->em->flush();
            $this->addFlash('succes', 'Vos modifications ont bien été prises en compte !');
            return $this->redirectToRoute('main_jobs');

        }
        return $this->render('pages/jobs/edit_job.html.twig', [
            "form" => $form->createView(),
            "currentUser" => $currentUser
        ]);
    }

    /**
     * @Route("/add/job", name="job_add")
     */
    public function addJob(Request $request): Response
    {
        $currentUser = $this->getUser();
        $job = new Jobs();
        $form = $this->createForm(AddJobType::class, $job);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $req = $form->getData();
            $this->em->persist($req);
            $this->em->flush();
            $this->addFlash('succes', 'Métier ajouter !');
            return $this->redirectToRoute('main_jobs');
        }
        return $this->render('pages/jobs/add_job.html.twig', [
            "form" => $form->createView(),
            "currentUser" => $currentUser
        ]);
    }

    /**
     * @Route("/delete/job/{id}", name="job_delete")
     */
    public function deleteJobs(Request $request, int $id): Response
    {
        $job = $this->jobsRepository->findOneBy(['id' => $id]);
        $size = sizeof($job->getUsers());
        if ($size == 0) {
            $this->em->remove($job);
            $this->em->flush();
            $this->addFlash('succes', 'Vos modifications ont bien été prises en compte !');
            return $this->redirectToRoute('main_jobs');

        } else {
            return $this->redirectToRoute('main_jobs');
        }

    }
}