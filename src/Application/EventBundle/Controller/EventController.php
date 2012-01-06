<?php

namespace Application\EventBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Application\EventBundle\Entity\Event;
use Application\EventBundle\Entity\EventUser;
use Application\EventBundle\Form\EventType;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\View\DefaultView;
use Pagerfanta\Adapter\DoctrineORMAdapter;

/**
 * Event controller.
 *
 * @Route("/event")
 */
class EventController extends Controller
{
    /**
     * Lists all Event entities.
     *
     * @Route("/", name="event")
     * @Template()
     */
    public function indexAction()
    {
	
		$request = $this->getRequest();
		$page = $request->query->get('page');
		if( !$page ) $page = 1;
	
        $em = $this->getDoctrine()->getEntityManager();



		$query = $em->createQueryBuilder();
		$query->add('select', 'e')
		   ->add('from', 'ApplicationEventBundle:Event e')
		   ->andWhere('e.date_start > :date')->setParameter('date', date('Y-m-d H:i:s'))
		   ->add('orderBy', 'e.featured DESC, e.date_start ASC');
		

		
		
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
     * Lists all Event entities by city.
     *
     * @Route("/city/{id}", name="event_city")
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
		$query->add('select', 'e')
		   ->add('from', 'ApplicationEventBundle:Event e')
		   ->andWhere('e.date_start > :date')->setParameter('date', date('Y-m-d H:i:s'))
		   ->andWhere('e.city_id = :city_id')->setParameter('city_id', $id)
		   ->add('orderBy', 'e.featured DESC, e.date_start ASC');
		

		
		
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
		
		$users = false;
		if( $page == 1 ){
			$qb = $em->createQueryBuilder();
			$qb->add('select', 'u')
			   ->add('from', 'ApplicationUserBundle:User u')
			   ->add('orderBy', 'u.date_login DESC')
			   ->setMaxResults(10);
			
			$query = $qb->getQuery();
			$users = $query->getResult();
			//shuffle( $users );
			//$users = array_splice($users, 0, 7);
		}
		
	
        //$em = $this->getDoctrine()->getEntityManager();
        //$entities = $em->getRepository('ApplicationEventBundle:Event')->findAll();

	 	$twig = $this->container->get('twig'); 
	    $twig->addExtension(new \Twig_Extensions_Extension_Text);

        return array('city' => $city, 'country' => $country, 'pager' => $html, 'entities' => $entities, 'users' => $users);
    }

    /**
     * Finds and displays a Event entity.
     *
     * @Route("/{id}/show", name="event_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ApplicationEventBundle:Event')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $user = $em->getRepository('ApplicationUserBundle:User')->find($entity->getUserId());



		$apuntado = false;
		
		$session = $this->getRequest()->getSession();
		$session_id = $session->get('id');
		if( $session_id ){
			$db = $this->get('database_connection');
			$query = "SELECT eu.id FROM EventUser eu WHERE eu.user_id = " . $session_id . " AND event_id = " . (int)$id;
			$result = $db->query($query)->fetch();
			$apuntado = $result['id'];	
		}



		$qb = $em->createQueryBuilder();
		$qb->add('select', 'u')
		   ->add('from', 'ApplicationUserBundle:User u, ApplicationEventBundle:EventUser eu')
		   ->andWhere('u.id = eu.user_id')
		   ->andWhere('eu.event_id = :id')->setParameter('id', $id);
		$query = $qb->getQuery();
		$users = $query->getResult();


		// es diferente usuario, visitas + 1
		if( $session_id != $entity->getUserId() ){
			$entity->setVisits($entity->getVisits() + 1 );
			$em->persist($entity);
			$em->flush();
		}
		

        return array(
            'entity'      => $entity,
			'user'		  => $user,
			'users'       => $users,
			'apuntado'    => $apuntado
		);
    }

    /**
     * Displays a form to create a new Event entity.
     *
     * @Route("/new", name="event_new")
     * @Template()
     */
    public function newAction()
    {
	
		$session = $this->getRequest()->getSession();
		$session_id = $session->get('id');
		if( !$session_id ){
			return $this->redirect($this->generateUrl('user_welcome', array('back' => $_SERVER['REQUEST_URI'])));
		}

        $entity = new Event();
        $form   = $this->createForm(new EventType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
			'hours'   => array('07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','00','01','02','03','04','05','06'),
			'minutes'=> array('00','10','20','30','40','50')
        );
    }

    /**
     * Creates a new Event entity.
     *
     * @Route("/create", name="event_create")
     * @Method("post")
     * @Template("ApplicationEventBundle:Event:new.html.twig")
     */
    public function createAction()
    {

        $entity  = new Event();
        $request = $this->getRequest();
        $form    = $this->createForm(new EventType(), $entity);
        $form->bindRequest($request);

		// rellenar campos que faltan
		$session = $this->getRequest()->getSession();
		$user_id = $session->get('id');
		$entity->setUserId( $user_id );
		$entity->setDate( new \DateTime("now") );
		$entity->setFeatured( 0 );
		
		// corregir fecha
		$h_start = $request->request->get('h_start');
		$m_start = $request->request->get('m_start');
		$h_end = $request->request->get('h_end');
		$m_end = $request->request->get('m_end');
		$date_start = $entity->getDateStart();
		$date_end = $entity->getDateEnd();
		$entity->setDateStart(  new \DateTime( $date_start->format('Y-m-d') . ' ' . $h_start . ":" . $m_start . ':00' ) );
		$entity->setDateEnd(  new \DateTime( $date_end->format('Y-m-d') . ' ' . $h_end . ":" . $m_end . ':00' ) );

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('event_show', array('id' => $entity->getId())));
            
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Event entity.
     *
     * @Route("/{id}/edit", name="event_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ApplicationEventBundle:Event')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $editForm = $this->createForm(new EventType(), $entity);


		// fechas
		$date_start = $entity->getDateStart();
		$date_end = $entity->getDateEnd();

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
			'h_start'     => $date_start->format('H'),
			'm_start'     => $date_start->format('i'),
			'h_end'       => $date_end->format('H'),
			'm_end'       => $date_end->format('i'),
			'hours'   => array('07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','00','01','02','03','04','05','06'),
			'minutes'=> array('00','10','20','30','40','50')
        );
    }

    /**
     * Edits an existing Event entity.
     *
     * @Route("/{id}/update", name="event_update")
     * @Method("post")
     * @Template("ApplicationEventBundle:Event:edit.html.twig")
     */
    public function updateAction($id)
    {
	
	
		 $em = $this->getDoctrine()->getEntityManager();

	        $entity = $em->getRepository('ApplicationEventBundle:Event')->find($id);

	        if (!$entity) {
	            throw $this->createNotFoundException('Unable to find Post entity.');
	        }


			$session = $this->getRequest()->getSession();
			$user_id = $session->get('id');
			$admin = $session->get('admin');

			if( ( $entity->getUserId() == $user_id ) || $admin ){

		        $editForm   = $this->createForm(new EventType(), $entity);

		        $request = $this->getRequest();

		        $editForm->bindRequest($request);
		

				// corregir fecha
				$h_start = $request->request->get('h_start');
				$m_start = $request->request->get('m_start');
				$h_end = $request->request->get('h_end');
				$m_end = $request->request->get('m_end');
				$date_start = $entity->getDateStart();
				$date_end = $entity->getDateEnd();
				$entity->setDateStart(  new \DateTime( $date_start->format('Y-m-d') . ' ' . $h_start . ":" . $m_start . ':00' ) );
				$entity->setDateEnd(  new \DateTime( $date_end->format('Y-m-d') . ' ' . $h_end . ":" . $m_end . ':00' ) );



		        if ($editForm->isValid()) {
		            $em->persist($entity);
		            $em->flush();

		            return $this->redirect($this->generateUrl('event_show', array('id' => $id)));
		        }

		        return array(
		            'entity'      => $entity,
		            'edit_form'   => $editForm->createView(),
		        );

			}else{
				$url = $this->generateUrl('event_show', array('id' => $entity->getId()));
				return $this->redirect($url);
			}




	
	
    }

    /**
     * Deletes a Event entity.
     *
     * @Route("/{id}/delete", name="event_delete")
     */
    public function deleteAction($id)
    {
      	$em = $this->getDoctrine()->getEntityManager();
		$entity = $em->getRepository('ApplicationEventBundle:Event')->find($id);
		if (!$entity) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }
		
		$session = $this->getRequest()->getSession();
		$user_id = $session->get('id');
		$admin = $session->get('admin');
		
		if( ( $entity->getUserId() == $user_id ) || $admin ){
			// eliminar usuarios apuntados
			$query = "DELETE FROM ApplicationEventBundle:EventUser eu WHERE eu.event_id = " . (int)$id;
			$em->createQuery($query)->execute();

            $em->remove($entity);
            $em->flush();

			$url = $this->generateUrl('event');
		}else{
			$url = $this->generateUrl('event_show', array('id' => $entity->getId()));
			
		}
		return $this->redirect($url);


    }


    /**
     * Event go
     *
     * @Route("/{id}/go/{value}", name="event_go")
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
		
		$event = $em->getRepository('ApplicationEventBundle:Event')->find($id);
		if (!$event) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }
		
		

		
		$db = $this->get('database_connection');
		$query = "SELECT eu.id FROM EventUser eu WHERE eu.user_id = " . $session_id . " AND eu.event_id = " . (int)$id;
		$result = $db->query($query)->fetch();
		$id_apuntado = $result['id'];

		
		// esta registrado?
		if( $value ){
			if( !$id_apuntado ){
				// apuntar usuario
		        $entity = new EventUser();
		        $entity->setEventId($id);
				$entity->setUserId( $session_id );
				$entity->setDate( new \DateTime("now") );
				$em->persist($entity);
		        $em->flush();
			}
		}else if( $id_apuntado ){
			// quitar usuario
			$entity = $em->getRepository('ApplicationEventBundle:EventUser')->find($id_apuntado);
			$em->remove($entity);
			$em->flush();
		}
		
		
		
		
		// actualizar users
		$query = $em->createQuery("SELECT COUNT(eu) as total FROM ApplicationEventBundle:EventUser eu WHERE eu.event_id = :id");
		$query->setParameter('id', $id);
		$total = current($query->getResult());
		$total_users = $total['total'];
		

		$event->setUsers($total_users);
		$em->persist($event);
		$em->flush();
		
		
		

		$url = $this->generateUrl('event_show', array('id' => $id));
		return $this->redirect($url);


    }


}
