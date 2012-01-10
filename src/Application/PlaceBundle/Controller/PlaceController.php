<?php

namespace Application\PlaceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Application\PlaceBundle\Entity\Place;
use Application\PlaceBundle\Form\PlaceType;


use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\View\DefaultView;
use Pagerfanta\Adapter\DoctrineORMAdapter;

/**
 * Place controller.
 *
 * @Route("/place")
 */
class PlaceController extends Controller
{
    /**
     * Lists all Place entities.
     *
     * @Route("/", name="place")
     * @Template()
     */
    public function indexAction()
    {
	
		$request = $this->getRequest();
		$page = $request->query->get('page');
		if( !$page ) $page = 1;
	
        $em = $this->getDoctrine()->getEntityManager();



		$query = $em->createQueryBuilder();
		$query->add('select', 'p')
		   ->add('from', 'ApplicationPlaceBundle:Place p')
		   //->add('where', 'p.type != 2')
		   ->add('orderBy', 'p.featured DESC, p.id DESC');
		

		
        $adapter = new DoctrineORMAdapter($query);

		$pagerfanta = new Pagerfanta($adapter);
		$pagerfanta->setMaxPerPage(10); // 10 by default
		$maxPerPage = $pagerfanta->getMaxPerPage();

		$pagerfanta->setCurrentPage($page); // 1 by default
		$entities = $pagerfanta->getCurrentPageResults();
		$routeGenerator = function($page, $category_id) {
			$url = '?page='.$page;
		    return $url;
		};

		$view = new DefaultView();
		$html = $view->render($pagerfanta, $routeGenerator);
		


	 	$twig = $this->container->get('twig'); 
	    $twig->addExtension(new \Twig_Extensions_Extension_Text);
	
	
        



        return array('pager' => $html, 'entities' => $entities);
    }


   /**
     * Lists all Places entities by city.
     *
     * @Route("/city/{id}", name="place_city")
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
		   ->add('from', 'ApplicationPlaceBundle:Place p')
		   ->andWhere('p.city_id = :city_id')->setParameter('city_id', $id)
		   ->add('orderBy', 'p.featured DESC, p.id DESC');




        $adapter = new DoctrineORMAdapter($query);

		$pagerfanta = new Pagerfanta($adapter);
		$pagerfanta->setMaxPerPage(10); // 10 by default
		$maxPerPage = $pagerfanta->getMaxPerPage();

		$pagerfanta->setCurrentPage($page); // 1 by default
		$entities = $pagerfanta->getCurrentPageResults();
		$routeGenerator = function($page) {
			$url = '?page='.$page;
		    return $url;
		};

		$view = new DefaultView();
		$html = $view->render($pagerfanta, $routeGenerator);




	 	$twig = $this->container->get('twig'); 
	    $twig->addExtension(new \Twig_Extensions_Extension_Text);

        return array('city' => $city, 'country' => $country, 'pager' => $html, 'entities' => $entities );
    }

    /**
     * Finds and displays a Place entity.
     *
     * @Route("/{id}/show", name="place_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ApplicationPlaceBundle:Place')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Place entity.');
        }

		$user = $em->getRepository('ApplicationUserBundle:User')->find($entity->getUserId());

		// es diferente usuario, visitas + 1
		$session = $this->getRequest()->getSession();
		$session_id = $session->get('id');
		if( $session_id != $entity->getUserId() ){
			$entity->setVisits($entity->getVisits() + 1 );
			$em->persist($entity);
			$em->flush();
		}

        return array(
            'entity'      => $entity,
			'user'         => $user,        );
    }

    /**
     * Displays a form to create a new Place entity.
     *
     * @Route("/new", name="place_new")
     * @Template()
     */
    public function newAction()
    {
	
		$session = $this->getRequest()->getSession();
		$session_id = $session->get('id');
		if( !$session_id ){
			return $this->redirect($this->generateUrl('user_welcome', array('back' => $_SERVER['REQUEST_URI'])));
		}
	
        $entity = new Place();
        $form   = $this->createForm(new PlaceType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new Place entity.
     *
     * @Route("/create", name="place_create")
     * @Method("post")
     * @Template("ApplicationPlaceBundle:Place:new.html.twig")
     */
    public function createAction()
    {
        $entity  = new Place();
        $request = $this->getRequest();
        $form    = $this->createForm(new PlaceType(), $entity);
        $form->bindRequest($request);

		// rellenar campos que faltan
		$session = $this->getRequest()->getSession();
		$user_id = $session->get('id');
		$entity->setUserId( $user_id );
		$entity->setDate( new \DateTime("now") );
		$entity->setFeatured( 0 );


        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('place_show', array('id' => $entity->getId())));
            
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Place entity.
     *
     * @Route("/{id}/edit", name="place_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ApplicationPlaceBundle:Place')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Place entity.');
        }


		$session = $this->getRequest()->getSession();
		$user_id = $session->get('id');
		$admin = $session->get('admin');
		
		if( ( $entity->getUserId() == $user_id ) || $admin ){

	        $editForm = $this->createForm(new PlaceType(), $entity);


	        return array(
	            'entity'      => $entity,
	            'edit_form'   => $editForm->createView(),
	        );

		}else{
			$url = $this->generateUrl('place_show', array('id' => $entity->getId()));
			return $this->redirect($url);
		}

    }

    /**
     * Edits an existing Place entity.
     *
     * @Route("/{id}/update", name="place_update")
     * @Method("post")
     * @Template("ApplicationPlaceBundle:Place:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ApplicationPlaceBundle:Place')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Place entity.');
        }

		$session = $this->getRequest()->getSession();
		$user_id = $session->get('id');
		$admin = $session->get('admin');

		if( ( $entity->getUserId() == $user_id ) || $admin ){

        	$editForm   = $this->createForm(new PlaceType(), $entity);


	        $request = $this->getRequest();

	        $editForm->bindRequest($request);

	        if ($editForm->isValid()) {
	            $em->persist($entity);
	            $em->flush();

	            return $this->redirect($this->generateUrl('place_show', array('id' => $id)));
	        }

	        return array(
	            'entity'      => $entity,
	            'edit_form'   => $editForm->createView(),
	        );
		
		}else{
			$url = $this->generateUrl('place_show', array('id' => $entity->getId()));
			return $this->redirect($url);
		}
		
		
    }



    /**
     * Deletes a Place entity.
     *
     * @Route("/{id}/delete", name="place_delete")
     */
    public function deleteAction($id)
    {
		$em = $this->getDoctrine()->getEntityManager();
		$entity = $em->getRepository('ApplicationPlaceBundle:Place')->find($id);
		if (!$entity) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }
		
		$session = $this->getRequest()->getSession();
		$user_id = $session->get('id');
		$admin = $session->get('admin');
		
		if( ( $entity->getUserId() == $user_id ) || $admin ){

            $em->remove($entity);
            $em->flush();

			$url = $this->generateUrl('place');
		}else{
			$url = $this->generateUrl('place_show', array('id' => $entity->getId()));
			
		}
		return $this->redirect($url);
    }



    /**
     * Admin Place entities.
     *
     * @Route("/admin", name="place_admin")
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
		   ->add('from', 'ApplicationPlaceBundle:Place p')
		   ->add('orderBy', 'p.featured DESC, p.id DESC');
		

		
		
        $adapter = new DoctrineORMAdapter($query);

		$pagerfanta = new Pagerfanta($adapter);
		$pagerfanta->setMaxPerPage(10); // 10 by default
		$maxPerPage = $pagerfanta->getMaxPerPage();

		$pagerfanta->setCurrentPage($page); // 1 by default
		$entities = $pagerfanta->getCurrentPageResults();
		$routeGenerator = function($page) {//, $category_id
			$url = '?page='.$page;
			//if( $category_id ) $url .= '&c=' . $category_id;
		    return $url;
		};

		$view = new DefaultView();
		$html = $view->render($pagerfanta, $routeGenerator);//, array('category_id' => (int)$category_id)
		
		
		
	
        //$em = $this->getDoctrine()->getEntityManager();
        //$entities = $em->getRepository('ApplicationEventBundle:Event')->findAll();

	 	$twig = $this->container->get('twig'); 
	    $twig->addExtension(new \Twig_Extensions_Extension_Text);

        return array('pager' => $html, 'entities' => $entities);
    }


    /**
     * Feature Place entities.
     *
     * @Route("/admin/featured/{id}/{value}", name="place_admin_featured")
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
        $entity = $em->getRepository('ApplicationPlaceBundle:Place')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        
        $entity->setFeatured($value);
        $em->persist($entity);
		$em->flush();

		return $this->redirect( $_SERVER['HTTP_REFERER'] );
    }

}
