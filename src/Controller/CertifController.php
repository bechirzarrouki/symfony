<?php

namespace App\Controller;

use App\Entity\Certif;
use App\Entity\Cours;
use App\Repository\CertifRepository;
use App\Repository\CoursRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CertifController extends AbstractController
{
    #[Route('/certif', name: 'app_certif')]
    public function showcertif(CertifRepository $rep, CoursRepository $coursRepository): Response
    {
        $certifs = $rep->findAll();
        $courses = $coursRepository->findAll(); // Get all courses
        return $this->render('backoffice/certif/certif.html.twig', [
            'tabcertif' => $certifs,
            'tabcours'  => $courses, // Pass courses to the template
        ]);
    }

    #[Route('/deletecertif/{id}', name: 'app_deletecertif')]
    public function deletecertif($id, ManagerRegistry $manager, CertifRepository $rep): Response
    {
        $em = $manager->getManager();
        $certif = $rep->find($id);
        if ($certif) {
            $em->remove($certif);
            $em->flush();
        }
        return $this->redirectToRoute('app_certif');
    }

    #[Route('/addcertif', name: 'app_addcertif', methods: ['POST'])]
    public function addCertif(ManagerRegistry $manager, Request $request, CoursRepository $coursRepository): Response
    {
        $em = $manager->getManager();
        $certif = new Certif();

        $certif->setTitre($request->get('titre'));
        $date = \DateTime::createFromFormat('Y-m-d', $request->get('date'));
        $certif->setDate($date);
        $certif->setDuree($request->get('duree'));
        $certif->setNomUser($request->get('NomUser'));

        // Get the course ID from the form, then fetch the Cours entity
        $coursId = $request->get('IdCours');
        $cours = $coursRepository->find($coursId);
        $certif->setIdCours($cours);

        $em->persist($certif);
        $em->flush();

        return $this->redirectToRoute('app_certif');
    }

    #[Route('/editcertif/{id}', name: 'app_editcertif', methods: ['GET', 'POST'])]
    public function editCertif(ManagerRegistry $manager, Request $request, $id, CoursRepository $coursRepository): Response
    {
        $em = $manager->getManager();
        $certif = $manager->getRepository(Certif::class)->find($id);

        if (!$certif) {
            return $this->redirectToRoute('app_certif');
        }

        if ($request->isMethod('POST')) {
            $certif->setTitre($request->get('titre'));
            $date = \DateTime::createFromFormat('Y-m-d', $request->get('date'));
            $certif->setDate($date);
            $certif->setDuree($request->get('duree'));
            $certif->setNomUser($request->get('NomUser'));

            $coursId = $request->get('IdCours');
            $cours = $coursRepository->find($coursId);
            $certif->setIdCours($cours);

            $em->flush();
            return $this->redirectToRoute('app_certif');
        }

        // Fetch courses also for the dropdown in the edit modal
        $allCertifs = $manager->getRepository(Certif::class)->findAll();
        $courses = $coursRepository->findAll();

        return $this->render('backoffice/certif/certif.html.twig', [
            'certif'    => $certif,
            'tabcertif' => $allCertifs,
            'tabcours'  => $courses,
        ]);
    }
}
