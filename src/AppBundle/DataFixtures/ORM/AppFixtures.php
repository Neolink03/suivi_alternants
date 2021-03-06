<?php
/**
 * Created by Antoine Lamirault.
 */
namespace AppBundle\DataFixtures\ORM;
namespace AppBundle\DataFixtures\ORM;
use AppBundle\Entity\Course;
use AppBundle\Entity\Promotion;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;

class AppFixtures implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        Fixtures::load(
            [
                __DIR__ . '/realUsers.yml',
                __DIR__ . '/realCourses.yml',
                __DIR__ . '/realCompanyContacts.yml',
                __DIR__ . '/realPromotions.yml',
                /*
                __DIR__ . '/users.yml',
                __DIR__ . '/courses.yml',
                __DIR__ . '/companyContacts.yml',
                __DIR__ . '/promotions.yml',
                __DIR__ . '/workflow.yml',
                __DIR__ . '/applications.yml',
                __DIR__ . '/statusModifications.yml',
                __DIR__ . '/dataAttachments.yml',
                */
            ],
            $manager,
            [
                'providers' => [$this],
                'locale' => 'fr_FR'
            ]
        );
    }
}