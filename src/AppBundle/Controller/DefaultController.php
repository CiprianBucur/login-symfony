<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class DefaultController extends Controller {

	/**
	 * @Route("/{_locale}", name="home_locale")
	 */
	public function indexAction(Request $request, $_locale="en") {
		return $this->render('default/index.html.twig', array ('limba' => $request->getLocale()));
	}
	
	/**
	 * @Route("/{_locale}/create", name="create")
	 */
	public function createAction(Request $request, $_locale="en") {
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $translated = $this->get('translator')->trans('You must be logged in before posting') . ".";
            return $this->render('@App/Security/login.html.twig', array (
                'limba' => $request->getLocale(),
                'error' => array(
                    'messageKey' => $translated,
                    'messageData' => array (
                        'security' => $translated,
                    )
                ),
                'last_username' => ''));
        }
        else {
            return $this->render('default/create.html.twig', array('limba' => $request->getLocale()));
        }
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
