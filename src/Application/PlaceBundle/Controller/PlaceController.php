<?php

namespace Application\PlaceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Application\PlaceBundle\Entity\Place;
use Application\PlaceBundle\Entity\PlaceUser;
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
		

		if( $entities ){
			$total = count($entities);
			for( $i = 0; $i < $total; $i++ ){
				$qb = $em->createQueryBuilder();
				$qb->add('select', 'u')
				   ->add('from', 'ApplicationUserBundle:User u, ApplicationPlaceBundle:PlaceUser pu')
				   ->andWhere('u.id = pu.user_id')
				   ->andWhere('pu.place_id = :id')->setParameter('id', $entities[$i]->getId())
				   ->setMaxResults(12);
				$query = $qb->getQuery();
				$entities[$i]->users_list = $query->getResult();
			}
		}



	 	//$twig = $this->container->get('twig'); 
	    //$twig->addExtension(new \Twig_Extensions_Extension_Text);
	
	

		$qb = $em->createQueryBuilder();
		$qb->add('select', 'COUNT(p.id) AS total, c.name, c.id')
		   ->add('from', 'ApplicationPlaceBundle:Place p, ApplicationCityBundle:City c')
		   ->andWhere('p.city_id = c.id')
		   ->add('groupBy', 'c.id')
		   ->add('orderBy', 'total DESC')
		   ->setMaxResults(13);
		$cities = $qb->getQuery()->getResult();
    



        return array('cities' => $cities, 'pager' => $html, 'entities' => $entities);
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

		if( $entities ){
			$total = count($entities);
			for( $i = 0; $i < $total; $i++ ){
				$qb = $em->createQueryBuilder();
				$qb->add('select', 'u')
				   ->add('from', 'ApplicationUserBundle:User u, ApplicationPlaceBundle:PlaceUser pu')
				   ->andWhere('u.id = pu.user_id')
				   ->andWhere('pu.place_id = :id')->setParameter('id', $entities[$i]->getId())
				   ->setMaxResults(12);
				$query = $qb->getQuery();
				$entities[$i]->users_list = $query->getResult();
			}
		}

		$qb = $em->createQueryBuilder();
		$qb->add('select', 'COUNT(p.id) AS total, c.name, c.id')
		   ->add('from', 'ApplicationPlaceBundle:Place p, ApplicationCityBundle:City c')
		   ->andWhere('p.city_id = c.id')
		   ->add('groupBy', 'c.id')
		   ->add('orderBy', 'total DESC')
		   ->setMaxResults(13);
		$cities = $qb->getQuery()->getResult();


	 	//$twig = $this->container->get('twig'); 
	    //$twig->addExtension(new \Twig_Extensions_Extension_Text);

        return array('cities' => $cities, 'city' => $city, 'country' => $country, 'pager' => $html, 'entities' => $entities );
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


		$apuntado = false;
		
		$session = $this->getRequest()->getSession();
		$session_id = $session->get('id');
		if( $session_id ){
			$db = $this->get('database_connection');
			$query = "SELECT pu.id FROM PlaceUser pu WHERE pu.user_id = " . $session_id . " AND place_id = " . (int)$id;
			$result = $db->query($query)->fetch();
			$apuntado = $result['id'];	
		}



		$qb = $em->createQueryBuilder();
		$qb->add('select', 'u')
		   ->add('from', 'ApplicationUserBundle:User u, ApplicationPlaceBundle:PlaceUser pu')
		   ->andWhere('u.id = pu.user_id')
		   ->andWhere('pu.place_id = :id')->setParameter('id', $id)
		   ->add('orderBy', 'u.category_id ASC, u.name ASC');
		$query = $qb->getQuery();
		$users_aux = $query->getResult();
		
		$users = array();
		if( $users_aux ){
			foreach( $users_aux as $user ){
				$users[ $user->getCategoryId() ][] = $user;
			}
		}
		
		
		
		$city = $em->getRepository('ApplicationCityBundle:City')->find( $entity->getCityId() );
		
		$query = $em->createQuery("SELECT c.name FROM ApplicationCityBundle:Country c WHERE c.code = :code");
		$query->setParameters(array(
			'code' => $city->getCode()
		));
		$country = current( $query->getResult() );

		// es diferente usuario, visitas + 1
		$session = $this->getRequest()->getSession();
		$session_id = $session->get('id');
		if( $session_id != $entity->getUserId() ){
			$entity->setVisits($entity->getVisits() + 1 );
			$em->persist($entity);
			$em->flush();
		}

        return array(
            'entity'	=> $entity,
			'user'		=> $user,
			'users'		=> $users,
			'apuntado'	=> $apuntado,
			'city'		=> $city,
			'country'	=> $country
			);
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
			// eliminar usuarios apuntados
			$query = "DELETE FROM ApplicationPlaceBundle:PlaceUser pu WHERE pu.place_id = " . (int)$id;
			$em->createQuery($query)->execute();

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

	 	//$twig = $this->container->get('twig'); 
	    //$twig->addExtension(new \Twig_Extensions_Extension_Text);

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



    /**
     * Place go
     *
     * @Route("/{id}/go/{value}", name="place_go")
     */
    public function goAction($id,$value)
    {
		// esta logueado?
		$session = $this->getRequest()->getSession();
		$session_id = $session->get('id');
		if( !$session_id ){
			return $this->redirect($this->generateUrl('user_welcome', array('back' => $_SERVER['REQUEST_URI'])).'#alert');
		}
		
		$em = $this->getDoctrine()->getEntityManager();
		
		$place = $em->getRepository('ApplicationPlaceBundle:Place')->find($id);
		if (!$place) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }
		
		

		
		$db = $this->get('database_connection');
		$query = "SELECT pu.id FROM PlaceUser pu WHERE pu.user_id = " . $session_id . " AND pu.place_id = " . (int)$id;
		$result = $db->query($query)->fetch();
		$id_apuntado = $result['id'];

		
		// esta registrado?
		if( $value ){
			if( !$id_apuntado ){
				// apuntar usuario
		        $entity = new PlaceUser();
		        $entity->setPlaceId($id);
				$entity->setUserId( $session_id );
				$entity->setDate( new \DateTime("now") );
				$em->persist($entity);
		        $em->flush();
			}
		}else if( $id_apuntado ){
			// quitar usuario
			$entity = $em->getRepository('ApplicationPlaceBundle:PlaceUser')->find($id_apuntado);
			$em->remove($entity);
			$em->flush();
		}
		
		
		
		
		// actualizar users
		$query = $em->createQuery("SELECT COUNT(pu) as total FROM ApplicationPlaceBundle:PlaceUser pu WHERE pu.place_id = :id");
		$query->setParameter('id', $id);
		$total = current($query->getResult());
		$total_users = $total['total'];
		

		$place->setUsers($total_users);
		$em->persist($place);
		$em->flush();
		
		
		

		$url = $this->generateUrl('place_show', array('id' => $id));
		return $this->redirect($url);


    }
}
