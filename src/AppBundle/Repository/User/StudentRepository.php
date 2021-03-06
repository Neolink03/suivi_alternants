<?php

namespace AppBundle\Repository\User;

use AppBundle\Entity\User\Student;
use AppBundle\Entity\User;

/**
 * StudentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StudentRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByCourses(array $courses)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s')->from(Student::class, 's')
            ->andWhere('c.name IN (:courses)')
            ->join('s.applications', 'a')
            ->join('a.promotion', 'p')
            ->join('p.course', 'c');

        $coursesNames = [];
        foreach ($courses as $course) {
            $coursesNames[] = $course->getName();
        }
        $qb->setParameter('courses', $coursesNames);

        return $qb->getQuery()->getResult();
    }

    public function findByCoursesWithFilters(array $courses, array $data)
    {
        $parameters = [];

        $qb = $this->_em->createQueryBuilder();
        $qb->select('s')->from(Student::class, 's')
            ->andWhere('c.name IN (:courses)')
            ->join('s.applications', 'a')
            ->join('a.promotion', 'p')
            ->join('p.course', 'c');

        foreach ($data as $key => $value) {
            if (!is_null($data[$key])) {
                $qb->andWhere('s.' . $key . ' LIKE :' . $key);
                $parameters[$key] = '%' . $data[$key] . '%';
            }
        }

        $coursesNames = [];
        foreach ($courses as $course) {
            $coursesNames[] = $course->getName();
        }

        $parameters['courses'] = $coursesNames;

        $qb->setParameters($parameters);

        return $qb->getQuery()->getResult();
    }

    public function findByNameLike($nameKeyWord)
    {
        $qb = $this->createQueryBuilder('student');
        $qb->from(User::class, 'user')
            ->where('user.id = student.id')
            ->andwhere('((user.firstName LIKE :nameKeyWord) OR (user.lastName LIKE :nameKeyWord))')
            ->setParameter('nameKeyWord', "%$nameKeyWord%");
        return $qb->getQuery()->getResult();
    }
}
