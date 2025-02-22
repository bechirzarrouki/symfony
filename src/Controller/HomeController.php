<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
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
    return $this->render('retour/index.html.twig', [
        'controller_name' => 'MenuController',
    ]);
}
#[Route('/test', name: 'app_test')]
public function test(): Response
{
    return $this->render('test/test.html.twig', [
        'controller_name' => 'testController',
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
#[Route('/userpdf', name: 'app_userpdf')]
public function userpdf(): Response
{
    return $this->render('admin/userpdf.html.twig', [
        'controller_name' => 'userpdfController',
    ]);
}
#[Route('/userrows', name: 'app_userrows')]
public function userrows(): Response
{
    return $this->render('back/userrows.html.twig', [
        'controller_name' => 'userrowsController',
    ]);
}

#[Route('/edit', name: 'app_edit')]
public function edit(): Response
{
    return $this->render('login/edit.html.twig', [
        'controller_name' => 'editController',
    ]);
}


}