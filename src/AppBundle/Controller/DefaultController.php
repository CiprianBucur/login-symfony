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

		if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
			return new Response('<html><body>Foarte bine!</body></html>');
		}
		elseif ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
			return new Response('<html><body>Cookie - trebuie sa reincerci</body></html>');
		}
        throw $this->createAccessDeniedException();

//		return new Response('<html><body>bau-bau</body></html>');
		
	}

	/**
	* @Security("has_role('ROLE_USER')")
    * @Route("/user", name="userPage")
	*/
	public function userAction() {
		return new Response('<html><body>User page!</body></html>');
	}
}
