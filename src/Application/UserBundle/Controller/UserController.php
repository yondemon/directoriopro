<?php

namespace Application\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Application\UserBundle\Entity\User;
use Application\UserBundle\Entity\Contact;
use Application\UserBundle\Form\UserType;
use Application\UserBundle\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\View\DefaultView;
use Pagerfanta\Adapter\DoctrineORMAdapter;

/**
 * User controller.
 *
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * Lists all User entities.
     *
     * @Route("/", name="user")
     * @Template()
     */
    public function indexAction()
    {
		$request = Request::createFromGlobals();
		$page = $request->query->get('page');
		if( !$page ) $page = 1;
	
        $em = $this->getDoctrine()->getEntityManager();

        //$entities = $em->getRepository('ApplicationUserBundle:User')->findAll();
		$dql = "SELECT u FROM ApplicationUserBundle:User u ORDER BY u.id DESC";
        $query = $em->createQuery($dql);
        $adapter = new DoctrineORMAdapter($query);

		$pagerfanta = new Pagerfanta($adapter);
		$pagerfanta->setMaxPerPage(10); // 10 by default
		$maxPerPage = $pagerfanta->getMaxPerPage();

		$pagerfanta->setCurrentPage($page); // 1 by default
		$entities = $pagerfanta->getCurrentPageResults();
		$routeGenerator = function($page) {
		    return '?page='.$page;
		};
		$view = new DefaultView();
		$html = $view->render($pagerfanta, $routeGenerator);


	 	$twig = $this->container->get('twig'); 
	    $twig->addExtension(new \Twig_Extensions_Extension_Text);

        return array('entities' => $entities, 'pager' => $html, 'nav_user' => 1);
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}/show", name="user_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ApplicationUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

	 	$twig = $this->container->get('twig'); 
	    $twig->addExtension(new \Twig_Extensions_Extension_Text);
	
		/*
		// es diferencia usuario, visitas + 1
		$session = $this->getRequest()->getSession();
		$id = $session->get('id');
		if( $id != $entity->getId() ){
			$entity->setVisits($entity->getVisits() + 1 );
			$em = $this->get('doctrine.orm.entity_manager');
			$em->persist($entity);
			$em->flush();
		}
		*/	
		
		$contact_form_html = false;
		if( $entity->getCanContact() ){
			$session = $this->getRequest()->getSession();
			$contact = new \Application\UserBundle\Entity\Contact;
			$id = $session->get('id');
			if( $id ){
				$user_login = $em->getRepository('ApplicationUserBundle:User')->find($id);
				$contact->setName( $user_login->getName() );
				$contact->setEmail( $user_login->getEmail() );
			}
			$contact->setSubject( "Contacto desde betabeers" );
			$contact_form = $this->createForm(new ContactType(), $contact);
			$contact_form_html = $contact_form->createView();
		}
		

		


        return array(
            'entity'      => $entity,
			'contact_form' => $contact_form_html,
			);
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/new", name="user_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new User();
        $form   = $this->createForm(new UserType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/create", name="user_create")
     * @Method("post")
     * @Template("ApplicationUserBundle:User:new.html.twig")
     */
    public function createAction()
    {
        $entity  = new User();
        $request = $this->getRequest();
        $form    = $this->createForm(new UserType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('user_show', array('id' => $entity->getId())));
            
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/edit", name="user_edit")
     * @Template()
     */
    public function editAction()
    {
	
		// esta logueado?
		$session = $this->getRequest()->getSession();
		$id = $session->get('id');
		if( !$id ){
			return $this->redirect('/');
		}
		
		
	
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ApplicationUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createForm(new UserType(), $entity);
        //$deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        );
    }

    /**
     * Edits an existing User entity.
     *
     * @Route("/update", name="user_update")
     * @Method("post")
     * @Template("ApplicationUserBundle:User:edit.html.twig")
     */
    public function updateAction()
    {
	

		// esta logueado?
		$session = $this->getRequest()->getSession();
		$id = $session->get('id');
		if( !$id ){
			return $this->redirect('/');
		}
	
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ApplicationUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm   = $this->createForm(new UserType(), $entity);
        //$deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
		
            $em->persist($entity);
            $em->flush();

        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            //'delete_form' => $deleteForm->createView(),
			'updated' => 1
        );
    }

    /**
     * Search User entities.
     *
     * @Route("/search", name="user_search")
     * @Template()
     */
    public function searchAction()
    {
		$request = Request::createFromGlobals();
		$search = $request->query->get('q');
		$category_id = $request->query->get('c');
		
		$query = "SELECT p FROM ApplicationUserBundle:User p WHERE 1 = 1";
		
		if( $search ) $query .= " AND p.body LIKE '%".$search."%'";
		if( $category_id ) $query .= " AND p.category_id = " . $category_id;
		
		$query .= " ORDER BY p.id DESC";

		$entities = $this->get('doctrine')->getEntityManager()
		            ->createQuery($query)
		            ->getResult();
		
	 	$twig = $this->container->get('twig'); 
	    $twig->addExtension(new \Twig_Extensions_Extension_Text);
		
        return array('entities' => $entities, 'form_category' =>$category_id);
    }


    /**
     * Contact form
     *
     * @Route("/{id}/contact", name="user_contact")
     * @Template()
     */
    public function contactAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
		$entity = $em->getRepository('ApplicationUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

		$form = $this->createForm(new ContactType());
		$result = 'no';
		
		$request = $this->getRequest();
		if ($request->getMethod() == 'POST') {
	        $form->bindRequest($request);
	
			
			

	        if ($form->isValid()) {

				
				$values = $form->getData();
				//var_dump($values);
				
				$toEmail = $entity->getEmail();// 'gafeman@gmail.com';
				
				extract( $values );

				$header = 'From: ' . $email . " \r\n";
				$header .= "X-Mailer: PHP/" . phpversion() . " \r\n";
				$header .= "Mime-Version: 1.0 \r\n";
				$header .= "Content-Type: text/plain";

				$mensaje = "Este mensaje fue enviado por " . $name . ". \r\n";
				$mensaje .= "Su e-mail es: " . $email . "\r\n";
				$mensaje .= "Mensaje: " . $body . " \r\n";
				$mensaje .= "Enviado el " . date('d/m/Y', time());




				$result = @mail($toEmail, $subject, utf8_decode($mensaje), $header);
				
				
				
				
				
	        }
	    }
		
		
		
        return array(
			'form' => $form->createView(),
            'entity'      => $entity,
			'result'      => $result
			);


    }

	

    /**
     * Facebook connect login
     *
     * @Route("/login", name="user_login")
     * @Template()
     */
    public function loginAction()
    {

		require __DIR__ . '/../../../../vendor/facebook/examples/example.php';
		
		
		
		// login ok ?
		if( isset( $user_profile['id'] ) ){
			
			$session = $this->getRequest()->getSession();
			
			// existe usuario en la bd?
			$em = $this->getDoctrine()->getEntityManager();
			$user = $em->getRepository('ApplicationUserBundle:User')->findOneBy(array('facebook_id' => $user_profile['id']));
			
			if( !$user ){
				$user = $em->getRepository('ApplicationUserBundle:User')->findOneBy(array('email' => $user_profile['email']));
			}
			
			if( !$user ){

				if( !isset( $user_profile['location']['name'] ) ) $user_profile['location']['name'] = '';
				if( !isset( $user_profile['website'] ) ) $user_profile['website'] = '';
				
				// usuario referido
				$ref_id = $session->get('ref_id');
				if( !$ref_id ) $ref_id = 0;
				
				$user = new \Application\UserBundle\Entity\User;
				$user->setAdmin(0);
				$user->setFacebookId($user_profile['id']);
				$user->setCategoryId(1);
				$user->setEmail($user_profile['email']);
				$user->setName($user_profile['name']);
				$user->setLocation($user_profile['location']['name']);
				$user->setDate( new \DateTime("now") );
				$user->setVotes(0);
				$user->setVisits(0);
				$user->setUrl( $user_profile['website'] );
				$user->setFreelance(0);
				$user->setCanContact(1);
				$user->setRefId($ref_id);

				$em = $this->get('doctrine.orm.entity_manager');
				$em->persist($user);
				$em->flush();
				
				$url = $this->generateUrl('user_edit');
				
			}else{
				
				$url = $this->generateUrl('user_show', array('id' => $user->getId()));
			}
			
			//$session = $this->getRequest()->getSession();
			$session->set('id', $user->getId());
			$session->set('facebook_id', $user->getFacebookId());
			$session->set('name', $user->getName());
			$session->set('admin', $user->getAdmin());
			
			// redirigir al perfil
			
		}else{
			$url = $loginUrl;
		}
		
		return $this->redirect($url);
    }


    /**
     * Facebook connect logout
     *
     * @Route("/logout", name="user_logout")
     * @Template()
     */
    public function logoutAction()
    {
		$session = $this->getRequest()->getSession();
		$session->set('id',null);
		$session->set('facebook_id',null);
		$session->set('name',null);
		$session->set('admin',null);
		return $this->redirect( $this->generateUrl('post') );
	}


    /**
     * Invite contacts
     *
     * @Route("/invite", name="user_invite")
     * @Template()
     */
    public function inviteAction()
    {
		// esta logueado?
		$session = $this->getRequest()->getSession();
		$id = $session->get('id');
		if( !$id ){
			return $this->redirect('/');
		}
	
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('ApplicationUserBundle:User')->find($id);




		
		$query = "SELECT COUNT(u.id) AS total FROM User u WHERE u.ref_id = " . $id;
		$db = $this->get('database_connection');
		$result = $db->query($query)->fetch();
		//$total = $db->fetchAll($query);
		$total = $result['total'];
		
		
	    

        return array('entity' => $entity, 'total' => $total);
    }

    /**
     * Welcome invite
     *
     * @Route("/welcome", name="user_welcome")
     * @Template()
     */
    public function welcomeAction()
    {
		$request = Request::createFromGlobals();
		$ref_id = $request->query->get('ref_id');
		if( $ref_id ){
			
	        $em = $this->getDoctrine()->getEntityManager();
	        $entity = $em->getRepository('ApplicationUserBundle:User')->find($ref_id);
	
			if( $entity ){
				$session = $this->getRequest()->getSession();
				$session->set('ref_id', $ref_id);
			}
		}
		
		$query = "SELECT COUNT(u.id) AS total, u.category_id FROM User u GROUP BY u.category_id ORDER BY total DESC";
		$db = $this->get('database_connection');
        $categories = $db->fetchAll($query);



		$query = "SELECT u FROM ApplicationUserBundle:User u ORDER BY u.id DESC";
		$users = $this->get('doctrine')->getEntityManager()
		            ->createQuery($query)
					->setMaxResults(7)
		            ->getResult();
		
        return array('categories_aux' => $categories, 'users' => $users);
    }

}
