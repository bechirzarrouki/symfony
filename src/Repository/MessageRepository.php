<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function save(Message $message, bool $flush = true): void
    {
        $this->getEntityManager()->persist($message);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Message $message, bool $flush = true): void
    {
        $this->getEntityManager()->remove($message);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Récupérer tous les messages d'un chat
     */
    public function findMessagesByChat(int $chatId): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.chat = :chatId')
            ->setParameter('chatId', $chatId)
            ->orderBy('m.sentAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
