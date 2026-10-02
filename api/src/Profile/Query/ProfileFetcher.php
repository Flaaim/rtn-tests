<?php

declare(strict_types=1);

namespace App\Profile\Query;

use App\Profile\Entity\Profile\Role;
use Doctrine\DBAL\Connection;

/** @psalm-suppress UnusedClass */
final class ProfileFetcher implements ProfileFetcherInterface
{
    public function __construct(
        private readonly Connection $connection
    ) {}

    public function getProfile(string $id): array
    {
        $qb = $this->connection->createQueryBuilder();

        $result = $qb->select('p.id, p.email', 'p.status', 'u.date', 'un.network', 'un.identity', 'u.role')
            ->from('profiles', 'p')
            ->leftJoin('p', 'users', 'u', 'p.id = u.id')
            ->leftJoin('p', 'user_networks', 'un', 'p.id = un.user_id')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();
        if (false === $result) {
            return [];
        }

        return [
            'id' => $result['id'],
            'email' => $result['email'],
            'role' => $result['role'],
            'status' => $result['status'],
            'date' => $result['date'],
            'network' => [
                'name' => $result['network'],
                'identity' => $result['identity'],
            ],
        ];
    }

    public function getUsers(int $page, int $limit): array
    {
        $page = max(1, $page);
        $limit = min(max(1, $limit), 100);
        $offset = ($page - 1) * $limit;

        $qb = $this->connection->createQueryBuilder();

        $rows = $qb->select('u.id, u.email', 'u.status', 'u.role, u.date')
            ->from('users', 'u')
            ->orderBy('u.date', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->executeQuery()
            ->fetchAllAssociative();

        $countQb = $this->connection->createQueryBuilder();
        $totalCount = $countQb->select('COUNT(u.id)')
            ->from('users', 'u')
            ->executeQuery()
            ->fetchOne();

        return [
            'items' => $rows,
            'totalCount' => $totalCount,
        ];
    }

    public function getFullProfile(string $id): array
    {
        $qb = $this->connection->createQueryBuilder();

        $result = $qb->select('p.id, p.email', 'p.status as profile_status', 'u.date', 'un.network', 'un.identity', 'u.role', 'p.name', 'p.surname', 'u.password_hash', 'u.status as auth_status')
            ->from('profiles', 'p')
            ->leftJoin('p', 'users', 'u', 'p.id = u.id')
            ->leftJoin('p', 'user_networks', 'un', 'p.id = un.user_id')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();
        if (false === $result) {
            return [];
        }

        return [
            'id' => $result['id'],
            'email' => $result['email'],
            'role' => $result['role'],
            'profile_status' => $result['profile_status'],
            'date' => $result['date'],
            'network' => [
                'name' => $result['network'],
                'identity' => $result['identity'],
            ],
            'name' => $result['name'],
            'surname' => $result['surname'],
            'password_hash' => $result['password_hash'],
            'auth_status' => $result['auth_status'],
        ];
    }

    public function getLookupList(): array
    {
        $qb = $this->connection->createQueryBuilder();

        return $qb->select('p.id, p.email')
            ->from('profiles', 'p')
            ->orderBy('p.email', 'DESC')
            ->where('p.role = :role')
            ->setParameter('role', Role::USER)
            ->executeQuery()
            ->fetchAllAssociative();
    }
}
