<?php

namespace App\Repository;

use App\Entity\Chat;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chat>
 */
class ChatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chat::class);
    }

    public function save(Chat $chat, bool $flush = true): void
    {
        $this->getEntityManager()->persist($chat);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Chat $chat, bool $flush = true): void
    {
        $this->getEntityManager()->remove($chat);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Trouver un chat entre deux utilisateurs
     */
    public function findChatBetweenUsers(int $user1Id, int $user2Id): ?Chat
    {
        return $this->createQueryBuilder('c')
            ->where('(c.user1 = :user1 AND c.user2 = :user2) OR (c.user1 = :user2 AND c.user2 = :user1)')
            ->setParameter('user1', $user1Id)
            ->setParameter('user2', $user2Id)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function findByUser(User $user): array
{
    return $this->createQueryBuilder('c')
        ->where('c.user1 = :user OR c.user2 = :user')
        ->setParameter('user', $user)
        ->getQuery()
        ->getResult();
}

}
