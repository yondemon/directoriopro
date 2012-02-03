<?php

namespace Application\AnunciosBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Application\AnunciosBundle\Entity\Post;
use Application\UserBundle\Entity\User;
use Application\UserBundle\Entity\Contact;
use Application\AnunciosBundle\Form\PostType;
use Application\UserBundle\Form\ContactType;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\HttpFoundation\Request;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\View\DefaultView;
use Pagerfanta\Adapter\DoctrineORMAdapter;

define('CAT_OTHER',9);


function getSlug($str, $replace=array(' con ', ' de ', ' para ', ' y ', ' en ', ' of '), $delimiter='-') {
	if( !empty($replace) ) {
		$str = str_replace((array)$replace, ' ', $str);
	}

	$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
	$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
	$clean = strtolower(trim($clean, '-'));
	$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

	return $clean;
}

/**
 * Post controller.
 *
 * @Route("/post")
 */
class PostController extends Controller
{
    /**
     * Lists all Post entities.
     *
     * @Route("/", name="post")
     * @Template()
     */
    public function indexAction()
    {
		$request = $this->getRequest();
		$em = $this->getDoctrine()->getEntityManager();
		
		
		
		
		
		// esta logueado?
		$session = $request->getSession();

		$cookie_login = $request->cookies->get('login');
		if( !$session->get('id') && $cookie_login ){
			$cookie_login_info = explode(':',$cookie_login);
			$user = $em->getRepository('ApplicationUserBundle:User')->find($cookie_login_info[0]);
			
			$pass = $user->getPass();
			if( !$pass ) $pass = md5( $user->getDate()->format('Y-m-d H:i:s') );
			

			if( $cookie_login_info[1] == $pass ){
				$session = $this->getRequest()->getSession();
				$session->set('id', $user->getId());
				$session->set('name', $user->getShortName());
				$session->set('slug', $user->getSlug());
				$session->set('admin', $user->getAdmin());
			}	
		}
		


		
		$page = $request->query->get('page');
		if( !$page ) $page = 1;
	
        



		$query = $em->createQueryBuilder();
		$query->add('select', 'p')
		   ->add('from', 'ApplicationAnunciosBundle:Post p')
		   ->add('where', 'p.visible = 1')
		   ->add('orderBy', 'p.featured DESC, p.id DESC');
		
		// categoria?
		$category_id = $request->query->get('c');
		if( $category_id ){
		   $query->add('where', 'p.category_id = :category_id')->setParameter('category_id', $category_id);

		}
		
		
        $adapter = new DoctrineORMAdapter($query);

		$pagerfanta = new Pagerfanta($adapter);
		$pagerfanta->setMaxPerPage(10); // 10 by default
		$maxPerPage = $pagerfanta->getMaxPerPage();

		$pagerfanta->setCurrentPage($page); // 1 by default
		$entities = $pagerfanta->getCurrentPageResults();
		$routeGenerator = function($page, $category_id) {
			$url = '?page='.$page;
			if( $category_id ) $url .= '&c=' . $category_id;
		    return $url;
		};

		$view = new DefaultView();
		$html = $view->render($pagerfanta, $routeGenerator, array('category_id' => (int)$category_id));
		
		/*
		$users = false;
		if( $page == 1 ){
			$qb = $em->createQueryBuilder();
			$qb->add('select', 'u')
			   ->add('from', 'ApplicationUserBundle:User u')
			   ->add('where', 'u.freelance = 1')
			   ->andWhere("u.body != ''")
			   ->add('orderBy', 'u.date_login DESC')
			   ->setMaxResults(14);
			
			$query = $qb->getQuery();
			$users = $query->getResult();
			shuffle( $users );
			$users = array_splice($users, 0, 7);
		}
		*/

	 	$twig = $this->container->get('twig'); 
	    $twig->addExtension(new \Twig_Extensions_Extension_Text);

		
		$home = (!$category_id && $page == 1);

        return array('pager' => $html, 'entities' => $entities, 'home' => $home );
    }

    /**
     * Lists all Posts entities by city.
     *
     * @Route("/city/{id}", name="post_city")
     * @Template()
     */
    public function cityAction($id)
    {
		$request = $this->getRequest();
		$page = $request->query->get('page');
		if( !$page ) $page = 1;
	
        $em = $this->getDoctrine()->getEntityManager();

		$city = $em->getRepository('ApplicationCityBundle:City')->find($id);
		
		if(!$city){
			throw $this->createNotFoundException('Unable to find Post entity.');
		}


		
		$query = $em->createQuery("SELECT c.name FROM ApplicationCityBundle:Country c WHERE c.code = :code");
		$query->setParameters(array(
			'code' => $city->getCode()
		));
		$country = current( $query->getResult() );


		$query = $em->createQueryBuilder();
		$query->add('select', 'p')
		   ->add('from', 'ApplicationAnunciosBundle:Post p')
		   ->andWhere('p.city_id = :city_id')->setParameter('city_id', $id)
		   ->andWhere('p.visible = 1')
		   ->add('orderBy', 'p.featured DESC, p.id DESC');
		
		// categoria?
		$category_id = $request->query->get('c');
		if( $category_id ){
		   $query->add('where', 'p.category_id = :category_id')->setParameter('category_id', $category_id);

		}
		
		
        $adapter = new DoctrineORMAdapter($query);

		$pagerfanta = new Pagerfanta($adapter);
		$pagerfanta->setMaxPerPage(10); // 10 by default
		$maxPerPage = $pagerfanta->getMaxPerPage();

		$pagerfanta->setCurrentPage($page); // 1 by default
		$entities = $pagerfanta->getCurrentPageResults();
		$routeGenerator = function($page, $category_id) {
			$url = '?page='.$page;
			if( $category_id ) $url .= '&c=' . $category_id;
		    return $url;
		};

		$view = new DefaultView();
		$html = $view->render($pagerfanta, $routeGenerator, array('category_id' => (int)$category_id));
		



	 	$twig = $this->container->get('twig'); 
	    $twig->addExtension(new \Twig_Extensions_Extension_Text);

        return array('city' => $city, 'country' => $country, 'pager' => $html, 'entities' => $entities );
    }

    /**
     * Finds and displays a Post entity.
     *
     * @Route("/{id}/show", name="post_show2")
     * @Template()
     */
    public function show2Action($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('ApplicationAnunciosBundle:Post')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }
		return $this->redirect($this->generateUrl('post_show', array('id' => $entity->getID(), 'slug' => $entity->getSlug() )),301);
	}

    /**
     * Displays a form to create a new Post entity.
     *
     * @Route("/new", name="post_new")
     * @Template()
     */
    public function newAction()
    {
	
		$session = $this->getRequest()->getSession();
		$session_id = $session->get('id');
		if( !$session_id ){
			return $this->redirect($this->generateUrl('user_welcome', array('back' => $_SERVER['REQUEST_URI'])));
		}
		
		//si no es post
		$request = $this->getRequest();
		
		if ($request->getMethod() != 'POST') {
        	$em = $this->getDoctrine()->getEntityManager();
			$user = $em->getRepository('ApplicationUserBundle:User')->find($session_id);
			$email = $user->getEmail();
		}
	
		$type = $request->query->get('type') ? 1 : 0;
		
	
        $entity = new Post();
        $entity->setType($type);
		$entity->setEmail( $email );
        $form   = $this->createForm(new PostType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
			'type'   => $type
        );
    }

    /**
     * Creates a new Post entity.
     *
     * @Route("/create", name="post_create")
     * @Method("post")
     * @Template("ApplicationAnunciosBundle:Post:new.html.twig")
     */
    public function createAction()
    {
        $entity  = new Post();
        $request = $this->getRequest();
        $form    = $this->createForm(new PostType(), $entity);
        $form->bindRequest($request);

		// rellenar campos que faltan
		$session = $this->getRequest()->getSession();
		$user_id = $session->get('id');
		$entity->setUserId( $user_id );
		$entity->setDate( new \DateTime("now") );
		//$entity->setFeatured( 0 );
		

        if ($form->isValid()) {

			$em = $this->getDoctrine()->getEntityManager();
	
			$slug = $entity->getTitle();
			$city_id = $entity->getCityId();
			if( $city_id ){
				$city = $em->getRepository('ApplicationCityBundle:City')->find( $city_id );
				$slug .= ' ' . $city->getName();
			}
			$company = $entity->getCompany();
			if( $company ){
				$slug .= ' ' . $company;
			}
	
			$entity->setSlug( getSlug( $slug ) );
			
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('post_show', array('id' => $entity->getId(), 'slug' => $entity->getSlug())));
            
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Post entity.
     *
     * @Route("/{id}/edit", name="post_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ApplicationAnunciosBundle:Post')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }

		$session = $this->getRequest()->getSession();
		$user_id = $session->get('id');
		$admin = $session->get('admin');
		
		if( ( $entity->getUserId() == $user_id ) || $admin ){

			$editForm = $this->createForm(new PostType(), $entity);

	        return array(
	            'entity'      => $entity,
	            'edit_form'   => $editForm->createView(),
	        );
	
		}else{
			$url = $this->generateUrl('post_show', array('id' => $entity->getId(), 'slug' => $entity->getSlug()));
			return $this->redirect($url);
		}
		
		
		
 
    }

    /**
     * Edits an existing Post entity.
     *
     * @Route("/{id}/update", name="post_update")
     * @Method("post")
     * @Template("ApplicationAnunciosBundle:Post:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ApplicationAnunciosBundle:Post')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }

		
		$session = $this->getRequest()->getSession();
		$user_id = $session->get('id');
		$admin = $session->get('admin');
		
		if( ( $entity->getUserId() == $user_id ) || $admin ){

	        $editForm   = $this->createForm(new PostType(), $entity);

	        $request = $this->getRequest();

	        $editForm->bindRequest($request);

	        if ($editForm->isValid()) {
		
				$slug = $entity->getTitle();
				$city_id = $entity->getCityId();
				if( $city_id ){
					$city = $em->getRepository('ApplicationCityBundle:City')->find( $city_id );
					$slug .= ' ' . $city->getName();
				}
				$company = $entity->getCompany();
				if( $company ){
					$slug .= ' ' . $company;
				}
		
				$entity->setSlug( getSlug( $slug ) );
				
	            $em->persist($entity);
	            $em->flush();

	            return $this->redirect($this->generateUrl('post_show', array('id' => $id, 'slug' => $entity->getSlug())));
	        }

	        return array(
	            'entity'      => $entity,
	            'edit_form'   => $editForm->createView(),
	        );
	
		}else{
			$url = $this->generateUrl('post_show', array('id' => $entity->getId(), 'slug' => $entity->getSlug()));
			return $this->redirect($url);
		}
		
		
		
		

    }

    /**
     * Deletes a Post entity.
     *
     * @Route("/{id}/delete", name="post_delete")
     */
    public function deleteAction($id)
    {
		$em = $this->getDoctrine()->getEntityManager();
		$entity = $em->getRepository('ApplicationAnunciosBundle:Post')->find($id);
		if (!$entity) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }
		
		$session = $this->getRequest()->getSession();
		$user_id = $session->get('id');
		$admin = $session->get('admin');
		
		if( ( $entity->getUserId() == $user_id ) || $admin ){

            $em->remove($entity);
            $em->flush();

			$url = $this->generateUrl('post');
		}else{
			$url = $this->generateUrl('post_show', array('id' => $entity->getId(), 'slug' => $entity->getSlug()));
			
		}
		return $this->redirect($url);
    }

    /**
     * Search Post entities.
     *
     * @Route("/search", name="post_search")
     * @Template()
     */
    public function searchAction()
    {
		$request = $this->getRequest();
		$search = $request->query->get('q');
		$category_id = $request->query->get('c');
		$type = (int)$request->query->get('t');
		$location = $request->query->get('location');
		

		$em = $this->getDoctrine()->getEntityManager();
		$qb = $em->createQueryBuilder();
		$qb->add('select', 'p')
		   ->add('from', 'ApplicationAnunciosBundle:Post p')
		   ->add('where', 'p.visible = 1')
		   ->add('orderBy', 'p.featured DESC, p.id DESC');
		
		if( $search ) $qb->andWhere("( p.body LIKE '%".$search."%' OR p.title LIKE '%".$search."%' )");
		if( $category_id ) $qb->andWhere('p.category_id = :category_id')->setParameter('category_id', $category_id);
		if( $location ) $qb->andWhere("p.location = :location")->setParameter('location', $location);
		if( $type ) $qb->andWhere('p.type = :type')->setParameter('type', $type);

		$entities = $qb->getQuery()->getResult();
		
	 	$twig = $this->container->get('twig'); 
	    $twig->addExtension(new \Twig_Extensions_Extension_Text);
		
        return array('entities' => $entities, 'form_category' =>$category_id, 'form_type' => $type);
    }

    /**
     * Feed Post entities.
     *
     * @Route("/feed", name="post_feed", defaults={"_format"="xml"})
     * @Template()
     */
    public function feedAction()
    {

		$request = $this->getRequest();

		
		$em = $this->getDoctrine()->getEntityManager();

		$qb = $em->createQueryBuilder()
		   ->add('select', 'p')
		   ->add('from', 'ApplicationAnunciosBundle:Post p')
		   ->add('where', 'p.visible = 1')
		   ->add('orderBy', 'p.id DESC')
		   ->setMaxResults(10);
		
		// categoria?
		$category_id = $request->query->get('c');
		if( $category_id ){
		   $qb->andWhere('p.category_id = :category_id')->setParameter('category_id', $category_id);
		}
		
		// tipo?
		/*
		$type = (int)$request->query->get('t');
		if( $type ){
		   $qb->andWhere('p.type = :type')->setParameter('type', $type);
		}
		*/

		$query = $qb->getQuery();
		$entities = $query->getResult();

		
		
	 	$twig = $this->container->get('twig'); 
	    $twig->addExtension(new \Twig_Extensions_Extension_Text);
		
        return array('entities' => $entities, 'form_category' =>$category_id);
    }

    /**
     * Contact form
     *
     * @Route("/{id}/contact", name="post_contact")
     * @Template()
     */
    public function contactAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
		$entity = $em->getRepository('ApplicationAnunciosBundle:Post')->find($id);

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
				
				// backup
				@mail("gafeman@gmail.com", $subject, utf8_decode($mensaje), $header);
				
				
				// contabilizar contacto
				$entity->setInterested( $entity->getInterested() + 1 );
				$em->persist($entity);
				$em->flush();
				

	
	        }
	    }

        return array(
			'form' => $form->createView(),
            'entity'      => $entity,
			'result'      => $result,
			);


    }

    

    /**
     * Admin Post entities.
     *
     * @Route("/admin", name="post_admin")
     * @Template()
     */
    public function adminAction()
    {
	
		$session = $this->getRequest()->getSession();
		if( !$session->get('admin') ){
			return $this->redirect('/');
		}
	
	
		$request = $this->getRequest();
		$page = $request->query->get('page');
		if( !$page ) $page = 1;
	
        $em = $this->getDoctrine()->getEntityManager();




		$query = $em->createQueryBuilder();
		$query->add('select', 'p')
		   ->add('from', 'ApplicationAnunciosBundle:Post p')
		   //->add('where', 'p.type = 0')
		   ->add('orderBy', 'p.featured DESC, p.id DESC');
		
		// categoria?
		$category_id = $request->query->get('c');
		if( $category_id ){
		   $query->add('where', 'p.category_id = :category_id')->setParameter('category_id', $category_id);
		}


		
		
        $adapter = new DoctrineORMAdapter($query);
        $pagerfanta = new Pagerfanta($adapter);
		$pagerfanta->setMaxPerPage(20); // 10 by default
		$maxPerPage = $pagerfanta->getMaxPerPage();

		$pagerfanta->setCurrentPage($page); // 1 by default
		$entities = $pagerfanta->getCurrentPageResults();
		$routeGenerator = function($page,$category_id) {
			$url = '?page='.$page;
			if( $category_id ) $url .= '&c=' . $category_id;
		    return $url;
		};
		$view = new DefaultView();
		$html = $view->render($pagerfanta, $routeGenerator, array('category_id' => (int)$category_id));
		
		// estadisticas de anuncios
		$query = "SELECT COUNT(p.id) AS total, p.category_id FROM Post p GROUP BY p.category_id ORDER BY total DESC";
		$db = $this->get('database_connection');
        $categories = $db->fetchAll($query);


	 	$twig = $this->container->get('twig'); 
	    $twig->addExtension(new \Twig_Extensions_Extension_Text);

        return array('categories_aux' => $categories, 'pager' => $html, 'entities' => $entities);
    }

    /**
     * Feature Post entities.
     *
     * @Route("/admin/featured/{id}/{value}", name="post_admin_featured")
     * @Template()
     */
    public function featuredAction($id,$value)
    {
	
		$session = $this->getRequest()->getSession();
		if( !$session->get('admin') ){
			return $this->redirect('/');
		}
	
		// existe post?
		$em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('ApplicationAnunciosBundle:Post')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        
        $entity->setFeatured($value);
        $em->persist($entity);
		$em->flush();

		return $this->redirect( $_SERVER['HTTP_REFERER'] );
    }

    /**
     * Visible Post entities.
     *
     * @Route("/admin/visible/{id}/{value}", name="post_admin_visible")
     * @Template()
     */
    public function visibleAction($id,$value)
    {
	
		$session = $this->getRequest()->getSession();
		if( !$session->get('admin') ){
			return $this->redirect('/');
		}
	
		// existe post?
		$em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('ApplicationAnunciosBundle:Post')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        
        $entity->setVisible($value);
        $em->persist($entity);
		$em->flush();

		return $this->redirect( $_SERVER['HTTP_REFERER'] );
    }

    /**
     * about page
     *
     * @Route("/about", name="post_about")
     * @Template()
     */
    public function aboutAction()
    {
        return array();
    }

    /**
     * how page
     *
     * @Route("/how", name="post_how")
     * @Template()
     */
    public function howAction()
    {
        return array();
    }




    /**
     * Admin Stats
     *
     * @Route("/stats", name="post_stats")
     * @Template()
     */
    public function statsAction()
    {
	
		$session = $this->getRequest()->getSession();
		if( !$session->get('admin') ){
			return $this->redirect('/');
		}
	
		$em = $this->getDoctrine()->getEntityManager();
	
		// usuarios registrados mes
		$query = $em->createQueryBuilder();
		$query->add('select', 'COUNT(u.id) AS total, u.date')
		   ->add('from', 'ApplicationUserBundle:User u')
		   ->andWhere("u.date BETWEEN '" . date('Y-m-d',strtotime("-1 month")) . "00:00:00' AND '" . date('Y-m-d') . " 23:59:59'")
		   ->groupBy('u.date');
		$users_month_aux = $query->getQuery()->getResult();

		$users_month = array();
		if( $users_month_aux ){
			foreach( $users_month_aux as $item ){
				$k = (int)substr($item['date'],8,2);
				if( !isset( $users_month[$k] ) ) $users_month[$k] = 1;
				else $users_month[$k] += $item['total'];
			}
		}
		
		// ofertas publicadas mes
		$query = $em->createQueryBuilder();
		$query->add('select', 'COUNT(p.id) AS total, p.date')
		   ->add('from', 'ApplicationAnunciosBundle:Post p')
		   ->andWhere("p.date BETWEEN '" . date('Y-m-d',strtotime("-1 month")) . "00:00:00' AND '" . date('Y-m-d') . " 23:59:59'")
		   ->groupBy('p.date');
		$posts_month_aux = $query->getQuery()->getResult();

		$posts_month = array();
		if( $posts_month_aux ){
			foreach( $posts_month_aux as $item ){
				$k = (int)substr($item['date'],8,2);
				if( !isset( $posts_month[$k] ) ) $posts_month[$k] = 1;
				else $posts_month[$k] += $item['total'];
			}
		}

		$db = $this->get('database_connection');

		// usuarios registrados
		$query = "SELECT COUNT(u.id) AS total FROM User u";
		$result = $db->query($query)->fetch();
		$total_users = $result['total'];

		// usuarios referidos
		$query = "SELECT COUNT(u.id) AS total FROM User u WHERE u.ref_id IS NOT NULL";
		$result = $db->query($query)->fetch();
		$total_ref = $result['total'];

		// usuarios facebook
		$query = "SELECT COUNT(u.id) AS total FROM User u WHERE u.facebook_id IS NOT NULL";
		$result = $db->query($query)->fetch();
		$total_fb = $result['total'];

		// buscan empleo
		$query = "SELECT COUNT(u.id) AS total FROM User u WHERE u.unemployed = 1";
		$result = $db->query($query)->fetch();
		$total_unemployed = $result['total'];

		// freelance
		$query = "SELECT COUNT(u.id) AS total FROM User u WHERE u.freelance = 1";
		$result = $db->query($query)->fetch();
		$total_freelance = $result['total'];

		// recomendados
		$query = "SELECT COUNT(c.id) AS total FROM Comment c";
		$result = $db->query($query)->fetch();
		$total_comments = $result['total'];


		// anuncios
		$query = "SELECT COUNT(p.id) AS total FROM Post p";
		$result = $db->query($query)->fetch();
		$total_posts = $result['total'];

		// freelance
		$query = "SELECT COUNT(p.id) AS total FROM Post p WHERE p.type = 1";
		$result = $db->query($query)->fetch();
		$total_posts_freelance = $result['total'];

		// practicas
		$query = "SELECT COUNT(p.id) AS total FROM Post p WHERE p.type = 2";
		$result = $db->query($query)->fetch();
		$total_posts_internship = $result['total'];
		
		// eventos
		$query = "SELECT COUNT(e.id) AS total FROM Event e";
		$result = $db->query($query)->fetch();
		$total_events = $result['total'];

		// apuntados
		$query = "SELECT COUNT(e.id) AS total FROM EventUser e";
		$result = $db->query($query)->fetch();
		$total_joined = $result['total'];

		// places
		$query = "SELECT COUNT(p.id) AS total FROM Place p";
		$result = $db->query($query)->fetch();
		$total_places = $result['total'];
		
		
		// top posts
		$query = $em->createQueryBuilder();
		$query->add('select', 'p')
		   ->add('from', 'ApplicationAnunciosBundle:Post p')
		   ->add('orderBy', 'p.visits DESC')
		   ->setMaxResults(10);
		$top_posts = $query->getQuery()->getResult();
		

		// top cities posts
		$query = "SELECT COUNT(p.id) AS total, c.name, c.id FROM Post p, City c WHERE p.city_id = c.id GROUP BY c.id ORDER BY total DESC LIMIT 10";
        $cities = $db->fetchAll($query);
		



        return array(
			'total_places' => $total_places, 'total_events' => $total_events, 'total_joined' => $total_joined, 'posts_month' => $posts_month, 'cities' => $cities, 'top_posts' => $top_posts, 'users_month' => $users_month, 'total_users' => $total_users, 'total_ref' => $total_ref, 'total_fb' => $total_fb, 'total_unemployed' => $total_unemployed,
	        'total_freelance' => $total_freelance, 'total_comments' => $total_comments, 'total_posts' => $total_posts, 'total_posts_freelance' => $total_posts_freelance, 'total_posts_internship' => $total_posts_internship
	    	);
    }



    /**
     * Newsletter
     *
     * @Route("/newsletter", name="newsletter_show")
     * @Template()
     */
    public function newsletterAction()
    {

		$request = $this->getRequest();
		$id = $request->query->get('id');

		$em = $this->getDoctrine()->getEntityManager();
	
		// ciudad y pais
		$city = $country = false;
		if( $id ){
			$city = $em->getRepository('ApplicationCityBundle:City')->find($id);
			$query = $em->createQuery("SELECT c.name FROM ApplicationCityBundle:Country c WHERE c.code = :code");
			$query->setParameters(array(
				'code' => $city->getCode()
			));
			$country = current( $query->getResult() );
		}

		// eventos
		$qb = $em->createQueryBuilder();
		$qb->add('select', 'e')
		   ->add('from', 'ApplicationEventBundle:Event e')
		   ->andWhere('e.date_start > :date')->setParameter('date', date('Y-m-d H:i:s'))
		   ->andWhere('e.hashtag != :hashtag')->setParameter('hashtag', 'betabeers')
		   ->add('orderBy', 'e.featured DESC, e.date_start ASC')
		   ->setMaxResults(10);
		
		if( $id ){
			$qb->andWhere('e.city_id = :city_id')->setParameter('city_id', $id);
		}
		
		$events = $qb->getQuery()->getResult();
		
		// eventos betabeers
		$qb = $em->createQueryBuilder();
		$qb->add('select', 'e')
		   ->add('from', 'ApplicationEventBundle:Event e')
		   ->andWhere('e.date_start > :date')->setParameter('date', date('Y-m-d H:i:s'))
		   ->andWhere('e.hashtag = :hashtag')->setParameter('hashtag', 'betabeers')
		   ->add('orderBy', 'e.featured DESC, e.date_start ASC');
		
		if( $id ){
			$qb->andWhere('e.city_id = :city_id')->setParameter('city_id', $id);
		}
		
		$events2 = $qb->getQuery()->getResult();

		// anuncios
		$qb = $em->createQueryBuilder();
		$qb->add('select', 'p')
		   ->add('from', 'ApplicationAnunciosBundle:Post p')
		   ->add('where', 'p.visible = 1')
		   ->add('orderBy', 'p.featured DESC, p.id DESC')
		   ->setMaxResults(5);
		
		if( $id ){
			$qb->andWhere('p.city_id = :city_id')->setParameter('city_id', $id);
		}
		
		$posts = $qb->getQuery()->getResult();

		// users
		$qb = $em->createQueryBuilder();
		$qb->add('select', 'u')
		   ->add('from', 'ApplicationUserBundle:User u')
		   ->andWhere("u.body != ''")
		   ->andWhere("u.category_id != 13")
		   ->andWhere("u.twitter_url IS NOT NULL")
		   ->andWhere("u.url IS NOT NULL")
		   ->add('orderBy', 'u.date_login DESC')
		   ->setMaxResults(25);
		
		if( $id ){
			$qb->andWhere('u.city_id = :city_id')->setParameter('city_id', $id);
		}
		
		$users = $qb->getQuery()->getResult();
		shuffle( $users );
		$users = array_splice($users, 0, 9);

		// google group
		$threads = simplexml_load_file('https://groups.google.com/group/beta-beers/feed/rss_v2_0_topics.xml');
		$threads = $threads->channel->item;

	 	$twig = $this->container->get('twig'); 
	    $twig->addExtension(new \Twig_Extensions_Extension_Text);		

		return array('city' => $city, 'country' => $country, 'events' => $events, 'events2' => $events2, 'posts' => $posts, 'users' => $users, 'threads' => $threads );
	}





    /**
     * Posts widget
     *
     * @Route("/widget", name="post_widget")
     * @Template()
     */
    public function widgetAction()
    {
		$request = $this->getRequest();
		$em = $this->getDoctrine()->getEntityManager();
		


		$query = $em->createQueryBuilder();
		$query->add('select', 'p')
		   ->add('from', 'ApplicationAnunciosBundle:Post p')
		   ->add('orderBy', 'p.featured DESC, p.id DESC')
		   ->andWhere('p.visible = 1')
		   ->andWhere('p.type = 0')
		   ->setMaxResults(10);
		$entities = $query->getQuery()->getResult();


	 	$twig = $this->container->get('twig'); 
	    $twig->addExtension(new \Twig_Extensions_Extension_Text);


        return array('entities' => $entities );
    }

    /**
     * Get post slugs
     *
     * @Route("/slugs", name="post_slugs")
     */
    public function slugs()
    {
		$em = $this->getDoctrine()->getEntityManager();
		$qb = $em->createQueryBuilder()
		   ->add('select', 'p')
		   ->add('from', 'ApplicationAnunciosBundle:Post p')
		   ->add('orderBy', 'p.id ASC');

		$entities = $qb->getQuery()->getResult();
		$total = count( $entities );
		
		for( $i = 0; $i < $total; $i++ ){
			
			$slug = $entities[$i]->getTitle();
			$city_id = $entities[$i]->getCityId();
			if( $city_id ){
				$city = $em->getRepository('ApplicationCityBundle:City')->find( $city_id );
				$slug .= ' ' . $city->getName();
			}
			$company = $entities[$i]->getCompany();
			if( $company ){
				$slug .= ' ' . $company;
			}


			$entities[$i]->setSlug( getSlug( $slug ) );
			$em->persist($entities[$i]);
			$em->flush();
			
			//echo $entities[$i]->getSlug(),'<br>';
		}
		die();
	}
	
    /**
     * Finds and displays a Post entity.
     *
     * @Route("/{slug}-{id}/", requirements={"slug" = "[a-z0-9\-]+", "id" = "^\d+$"}, name="post_show")
     * @Template()
     */
    public function showAction($slug, $id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ApplicationAnunciosBundle:Post')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }

		$user = $em->getRepository('ApplicationUserBundle:User')->find($entity->getUserId());


		$session = $this->getRequest()->getSession();
		$contact = new \Application\UserBundle\Entity\Contact;
		$id = $session->get('id');
		if( $id ){
			$user_login = $em->getRepository('ApplicationUserBundle:User')->find($id);
			$contact->setName( $user_login->getName() );
			$contact->setEmail( $user_login->getEmail() );
		}
		$contact->setSubject( "RE: " . $entity->getTitle() );
		$contact_form = $this->createForm(new ContactType(), $contact);
		$contact_form_html = $contact_form->createView();


		
		$entities = false;
		$users = false;

		// ofertas relacionadas
		if( $entity->getType() == 0 ){
			$query = $em->createQueryBuilder();
			$query->add('select', 'p')
			   ->add('from', 'ApplicationAnunciosBundle:Post p')
			   ->add('where', 'p.category_id = :category_id')->setParameter('category_id', $entity->getCategoryId())
			   ->andWhere('p.id != :id')->setParameter('id', $entity->getId())
			   ->add('orderBy', 'p.id DESC')
			   ->setMaxResults(5);
			$entities = $query->getQuery()->getResult();
			
		}

		// es diferente usuario, visitas + 1
		$session = $this->getRequest()->getSession();
		$session_id = $session->get('id');
		if( $session_id != $entity->getUserId() ){
			$entity->setVisits($entity->getVisits() + 1 );
			$em->persist($entity);
			$em->flush();
		}

        return array(
            'entity'       => $entity,
            'user'         => $user,
			'contact_form' => $contact_form_html,
			'entities'     => $entities
			//'users'        => $users
			);
    }

}