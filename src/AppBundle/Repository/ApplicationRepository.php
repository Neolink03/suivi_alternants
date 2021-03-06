<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Application;
use AppBundle\Entity\Promotion;
use AppBundle\Entity\State;
use AppBundle\Entity\User\Student;

/**
 * ApplicationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ApplicationRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByPromotionAndFilters(Promotion $promotion, array $data)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('a')->from(Application::class, 'a')
            ->andWhere('s.lastName LIKE :lastName')
            ->andWhere('s.firstName LIKE :firstName')
            ->andWhere('a.currentState LIKE :currentState')
            ->andWhere('p.id = :promotion')
            ->join('a.student', 's')
            ->join('a.promotion', 'p');

        $qb->setParameter('lastName', '%' . $data['lastName'] . '%');
        $qb->setParameter('firstName', '%' . $data['firstName'] . '%');
        $qb->setParameter('currentState', $data['currentState'] ? $data['currentState']->getMachineName() : '%%');
        $qb->setParameter('promotion', $promotion->getId());

        return $qb->getQuery()->getResult();
    }

    public function findByPromotionAndFiltersWhereJuryCanEdit(Promotion $promotion, array $data)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('a')->from(Application::class, 'a')
            ->from(State::class, 'state')
            ->andWhere('s.lastName LIKE :lastName')
            ->andWhere('s.firstName LIKE :firstName')
            ->andWhere('a.currentState LIKE :currentState')
            ->andWhere('a.currentState = state.machineName')
            ->andWhere('p.id = :promotion')
            ->andWhere('state.juryCanEdit = TRUE')
            ->join('a.student', 's')
            ->join('a.promotion', 'p');

        $qb->setParameter('lastName', '%' . $data['lastName'] . '%');
        $qb->setParameter('firstName', '%' . $data['firstName'] . '%');
        $qb->setParameter('currentState', $data['currentState'] ? $data['currentState']->getMachineName() : '%%');
        $qb->setParameter('promotion', $promotion->getId());

        return $qb->getQuery()->getResult();
    }

    public function findAllWhereJuryCanEdit(Promotion $promotion){
        $qb = $this->_em->createQueryBuilder();
        $qb->select('a')->from(Application::class, 'a')
            ->from(State::class, 's')
            ->join('a.promotion', 'p')
            ->andWhere('p.id = :promotion')
            ->andWhere('a.currentState = s.machineName')
            ->andWhere('s.juryCanEdit = TRUE');


        $qb->setParameter('promotion', $promotion->getId());

        return $qb->getQuery()->getResult();
    }

    public function findByEmail(Promotion $promotion, Student $student){

        $qb = $this->_em->createQueryBuilder();
        $qb->select('a')->from(Application::class, 'a')
            ->from(Promotion::class, 'p')
            ->from(Student::class, 's')
            ->andWhere('a.promotion = :promotionEntity')
            ->andWhere('a.student = s')
            ->andWhere('s.email = :email');


        $qb->setParameter('promotionEntity', $promotion);
        $qb->setParameter('email', $student->getEmail());

        return $qb->getQuery()->getResult();
    }
}
