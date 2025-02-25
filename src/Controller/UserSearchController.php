<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserSearchController extends AbstractController
{
    #[Route('/search-user', name: 'search_user', methods: ['POST'])]
    public function searchUser(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = $request->toArray(); 
            $query=$data['query'];
            
            if (!$query) {
                return new JsonResponse(['message'=>$query]);
            }
    
            $users = $entityManager->getRepository(User::class)->createQueryBuilder('u')
                ->where('u.username LIKE :query')
                ->setParameter('query', '%' . $query . '%')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult();
                $data = array_map(function ($user) {
                    return [
                        'id' => $user->getId(),
                        'username' => $user->getUsername(),
                        'image'=>$user->getProfileImage(),
                    ];
                }, $users);

                if (empty($data)) {
                    return new JsonResponse(['message' => 'No users found'], 200);
                }
                return new JsonResponse($data);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
    
}
