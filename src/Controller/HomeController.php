<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_investisement')]
    public function investisement(): Response
    {
        return $this->render('investisement/index.html.twig', [
            'controller_name' => 'investisementssController',
        ]);
    }
    #[Route('/investisementA', name: 'app_investisementA')]
    public function investisementA(): Response
    {
        return $this->render('investisement/investisement.html.twig', [
            'controller_name' => 'investisementController',
        ]);
    }
    #[Route('/investisementadmin', name: 'app_investisementadmin')]
    public function investisementadmin(): Response
    {
        return $this->render('backoffice/investissement/investisementadmin.html.twig', [
            'controller_name' => 'investisementadminController',
        ]);
    }
    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }
    #[Route('/search', name: 'app_search')]
    public function search(): Response
    {
        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
        ]);
    }
    #[Route('/menu', name: 'app_menu')]
    public function menu(): Response
    {
        return $this->render('menu/index.html.twig', [
            'controller_name' => 'MenuController',
        ]);
    }
    #[Route('/retour', name: 'app_retour')]
public function retour(): Response
{
    return $this->render('retour/retour.html.twig', [
        'controller_name' => 'RetourController',
    ]);
}
}
