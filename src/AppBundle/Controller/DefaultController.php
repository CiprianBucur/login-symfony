<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
	 * 
     */
    public function indexAction(Request $request) {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }

	/**
	* @Security("has_role('ROLE_ADMIN')")
    * @Route("/admin", name="adminPage")
	*/
	public function adminAction() {
		return new Response('<html><body>Admin page!</body></html>');
	}

	/**
	* @Security("has_role('ROLE_ADMIN')")
    * @Route("/ascuns", name="adminAscuns")
	*/
	public function ascunsAction() {
		return new Response('<html><body>Foarte bine!</body></html>');
	}

	/**
	* @Security("has_role('ROLE_USER')")
    * @Route("/user", name="userPage")
	*/
	public function userAction() {
		return new Response('<html><body>User page!</body></html>');
	}
}
