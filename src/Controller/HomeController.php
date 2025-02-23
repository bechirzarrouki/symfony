<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    #[Route('/back', name: 'app_home_back')]
    public function backhome(): Response
    {
        return $this->render('back/home.html.twig', [
            'controller_name' => 'homeController',
        ]);
    }
    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'homeController',
        ]);
    }
    #[Route('/investisementafficher', name: 'app_investisementafficher')]
    public function investisementA(): Response
    {
        return $this->render('investisement/investisement.html.twig', [
            'controller_name' => 'investisementController',
        ]);
    }
    #[Route('/message', name: 'app_message')]
    public function message(): Response
    {
        return $this->render('message/index.html.twig', [
            'controller_name' => 'messageController',
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
    #[Route('/retour', name: 'app_retour')]
public function retour(): Response
{
    return $this->render('retour/retour.html.twig', [
        'controller_name' => 'RetourController',
    ]);
}

#[Route('/register', name: 'app_register')]
public function registre(): Response
{
    return $this->render('login/register.html.twig', [
        'controller_name' => 'rgisterController',
    ]);

}
#[Route('/forgetpassword', name: 'app_forget_password')]
public function forgetpassword(): Response
{
    return $this->render('login/forgetpassword.html.twig', [
        'controller_name' => 'forgetpasswordController',
    ]);

}
#[Route('/dashboard', name: 'app_dashboard')]

public function dashboard(): Response
{
    return $this->render('back/dashboard.html.twig', [
        'controller_name' => 'dashboardController',
    ]);

    
}
#[Route('/edituser', name: 'app_edituser')]
public function edituser(): Response
{
    return $this->render('back/edituser.html.twig', [
        'controller_name' => 'edituserController',
    ]);

}
#[Route('/dashboard', name: 'app_userlist')]
public function userlsit(): Response
{
    return $this->render('back/dashboard.html.twig', [
        'controller_name' => 'dashboardController',
    ]);

}
#[Route('/logout', name: 'app_logout')]
public function logout(): Response
{
    return $this->render('back/logout.html.twig', [
        'controller_name' => 'logoutController',
    ]);

}
}

