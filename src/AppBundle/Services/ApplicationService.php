<?php
/**
 * User: Antoine Lamirault
 */

namespace AppBundle\Services;


use AppBundle\Entity\Application;
use AppBundle\Entity\State;
use AppBundle\Entity\StatusModification;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

class ApplicationService
{
    private $em;
    private $tcs;
    private $session;

    public function __construct(
        EntityManager $em,
        TransitionConditionService $tcs,
        Session $session
    )
    {
        $this->em = $em;
        $this->tcs = $tcs;
        $this->session = $session;
    }

    public function setState(Application $application, array $data){
        $authoriseChangeState =true;
        if($data['transition']->getCondition()){
            $authoriseChangeState = $this->tcs->isChecked($data['transition']->getCondition());
        }

        if($authoriseChangeState){
            $state = $data['transition']->getEndState();

            $statusModif = new StatusModification();
            $statusModif->setApplication($application);
            $statusModif->setComment($data['comment']);
            $statusModif->setDateTime(new \DateTime());
            $statusModif->setState($state);

            $application->addStatusModification($statusModif);
            $application->setCurrentState($state->getMachineName());

            $this->em->persist($application);
            $this->em->flush();
        }
        else{
            $this->session->getFlashBag()->add("danger", $data['transition']->getCondition()->getErrorMessage());
        }
        return $application;
    }
}