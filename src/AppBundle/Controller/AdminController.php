<?php

/**
 * Created by PhpStorm.
 * User: lp
 * Date: 06/03/2017
 * Time: 16:28
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Application;
use AppBundle\Entity\Course;
use AppBundle\Forms\Types\AdminNewUserType;
use AppBundle\Forms\Types\UserType;
use AppBundle\Models\AdminNewUserDto;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use AppBundle\Forms\Types\Courses\CourseType;
use AppBundle\Models\Dtos\Courses\Course as CourseDto;

class AdminController extends Controller
{
    public function courseListAction(Request $request){
        $courses = $this->getDoctrine()->getRepository(Course::class)->findAll();
        return $this->render('AppBundle:Admin:courseList.html.twig', [
            "courses" => $courses,
        ]);
    }
    
    public function createCourseAction(Request $request) {
        $form = $this->createForm(CourseType::class, new CourseDto());

        if ($request->isMethod('post')) {

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                
                $courseDto = $form->getData();
                
                try {
                    $this->get('app.factory.course')->saveNewCourse($courseDto);
                    $this->addFlash("success", "La formation a bien été créée.");
                    return new RedirectResponse($this->generateUrl("admin.home"));
                }
                catch (\Exception $e){
                    $this->addFlash("danger", "La formation existe déjà.");
                }
            }
        }
        
        return $this->render('AppBundle:Course:edit.html.twig', [
            "courseForm" => $form->createView(),
            "title" => "Créer une formation",
            "updateCourseActionUrl" => $this->generateUrl("admin.course.add")
        ]);
    }


    /**
     * @ParamConverter("application")
     */
    public function dumpWorkflowAction(Request $request, Application $application){
        $this->get('app.factory.workflow')->generateWorflowFromApplication($application);
    }

    public function userAddAction(Request $request)
    {
        $adminNewUserDto = new AdminNewUserDto();
        $form = $this->createForm(AdminNewUserType::class,$adminNewUserDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form['userType']->getData() == "responsable" && $form['phoneNumber']->getData() == null){
                $this->addFlash("danger", "Une information est manquante ou incomplète.");
            }else{
                $plainpassword = $this->get('app.password')->generate();
                $adminNewUserDto->setPassword($plainpassword);
                $this->get('app.factory.user')->saveFromAdmin($adminNewUserDto);
                unset($adminNewUserDto);
                unset($form);
                $adminNewUserDto = new AdminNewUserDto();
                $form = $this->createForm(AdminNewUserType::class,$adminNewUserDto);
            }

        }

        return $this->render('AppBundle:Admin:addUser.html.twig', [
            'form' => $form->createView()
        ]);
    }


}