<?php

namespace Application\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Application\UserBundle\Entity\User;



/**
 * Api controller.
 *
 * @Route("/api")
 */
class ApiController extends Controller
{	
    /**
     * Validate user login
     *
     * @Route("/login", name="api_login")
     * @Template()
     */
    public function loginAction()
    {
		$request = $this->getRequest();
		$email = $request->query->get('email');
		$pass = $request->query->get('pass');
		
		$em = $this->getDoctrine()->getEntityManager();
		$query = $em->createQuery("SELECT u FROM ApplicationUserBundle:User u WHERE u.email = :email AND u.pass = :pass");
		$query->setParameters(array(
			'email' => $email,
			'pass' => $pass
		));
		$result = $query->getResult();
		if( $result ){
			$user = current( $result );
			
			$twitter = $user->getTwitterUrl();
			if( $twitter ) $twitter = '@' . $twitter; 
			
			$profile = array(
				'name' => $user->getName(),
				'email' => $user->getEmail(),
				'url' => str_replace('http://','', $this->generateUrl('user_show', array('id' => $user->getId()), true)), //$user->getUrl()
				'twitter' => $twitter,
				'phone' => $user->getPhone(),
			);
			$response = array('result' => 'ok', 'profile' => $profile);

		}else{
			$response = array('result' => 'ko');
		}	
		die(json_encode($response));
	}
}