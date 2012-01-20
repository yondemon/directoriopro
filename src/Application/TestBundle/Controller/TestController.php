<?php

namespace Application\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Application\TestBundle\Entity\Test;
use Application\TestBundle\Form\TestType;


use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\View\DefaultView;
use Pagerfanta\Adapter\DoctrineORMAdapter;

/**
 * Test controller.
 *
 * @Route("/test")
 */
class TestController extends Controller
{
    /**
     * Lists all Test entities.
     *
     * @Route("/", name="test")
     * @Template()
     */
    public function indexAction()
    {
		$request = $this->getRequest();
        $em = $this->getDoctrine()->getEntityManager();

		$page = $request->query->get('page');
		if( !$page ) $page = 1;

		$query = $em->createQueryBuilder();
		$query->add('select', 't')
		   ->add('from', 'ApplicationTestBundle:Test t')
		   ->add('orderBy', 't.featured DESC, t.id DESC');
		
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

        return array('pager' => $html, 'entities' => $entities);
    }

    /**
     * Finds and displays a Test entity.
     *
     * @Route("/{id}/show", name="test_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ApplicationTestBundle:Test')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Test entity.');
        }

		$posts = false;
		if( $entity->getEnabled() ){
			$search = $entity->getTag();
			$query = $em->createQueryBuilder();
			$query->add('select', 'p')
			   ->add('from', 'ApplicationAnunciosBundle:Post p')
			   ->andWhere("( p.body LIKE '%".$search."%' OR p.title LIKE '%".$search."%' )")
			   ->add('orderBy', 'p.date DESC')
			   ->setMaxResults(5);
			$posts = $query->getQuery()->getResult();
		}
		

		$query = $em->createQueryBuilder();
		$query->add('select', 'u')
		   ->add('from', 'ApplicationUserBundle:User u, ApplicationTestBundle:TestUser tu')
		   ->add('where', 'u.id = tu.user_id AND tu.test_id = ' . $id)
		   ->add('orderBy', 'u.date DESC');
		   //->setMaxResults(14);
		$users = $query->getQuery()->getResult();


		// es diferente usuario, visitas + 1
		$session = $this->getRequest()->getSession();
		$session_id = $session->get('id');
		if( $session_id != $entity->getUserId() ){
			$entity->setVisits($entity->getVisits() + 1 );
			$em->persist($entity);
			$em->flush();
		}
		

	 	$twig = $this->container->get('twig'); 
	    $twig->addExtension(new \Twig_Extensions_Extension_Text);

        return array(
            'entity' => $entity,
			'posts' => $posts,
			'users' => $users
		);
    }

    /**
     * Finds and displays a Test entity.
     *
     * @Route("/{id}/take", name="test_take")
     * @Template()
     */
    public function takeAction($id)
    {
	
		$session = $this->getRequest()->getSession();
		$session_id = $session->get('id');
		if( !$session_id ){
			return $this->redirect($this->generateUrl('user_welcome', array('back' => $_SERVER['REQUEST_URI'])));
		}
	
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ApplicationTestBundle:Test')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Test entity.');
        }

		if( !$entity->getEnabled() ){
			return $this->redirect($this->generateUrl('test_show', array('id' => $entity->getId())));
		}


        return array(
            'entity'      => $entity
		);
    }

    /**
     * Displays a form to create a new Test entity.
     *
     * @Route("/new", name="test_new")
     * @Template()
     */
    public function newAction()
    {
	
		$session = $this->getRequest()->getSession();
		$session_id = $session->get('id');
		if( !$session_id ){
			return $this->redirect($this->generateUrl('user_welcome', array('back' => $_SERVER['REQUEST_URI'])));
		}
	
        $entity = new Test();
        $form   = $this->createForm(new TestType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new Test entity.
     *
     * @Route("/create", name="test_create")
     * @Method("post")
     * @Template("ApplicationTestBundle:Test:new.html.twig")
     */
    public function createAction()
    {
        $entity  = new Test();
        $request = $this->getRequest();
        $form    = $this->createForm(new TestType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();



			// rellenar campos que faltan
			$session = $this->getRequest()->getSession();
			$user_id = $session->get('id');
			$entity->setUserId( $user_id );
			$entity->setDate( new \DateTime("now") );


            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('test_show', array('id' => $entity->getId())));
            
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Test entity.
     *
     * @Route("/{id}/edit", name="test_edit")
     * @Template()
     */
    public function editAction($id)
    {
	
		$session = $this->getRequest()->getSession();
		$session_id = $session->get('id');
		if( !$session_id ){
			return $this->redirect($this->generateUrl('user_welcome', array('back' => $_SERVER['REQUEST_URI'])));
		}
	
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ApplicationTestBundle:Test')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Test entity.');
        }

        $editForm = $this->createForm(new TestType(), $entity);


        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Edits an existing Test entity.
     *
     * @Route("/{id}/update", name="test_update")
     * @Method("post")
     * @Template("ApplicationTestBundle:Test:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ApplicationTestBundle:Test')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Test entity.');
        }

        $editForm   = $this->createForm(new TestType(), $entity);


        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('test_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }





    /**
     * finish test
     *
     * @Route("/{id}/result", name="test_result")
     * @Template("ApplicationTestBundle:Test:result.html.twig")
     * @Method("post")
     */
    public function resultAction($id)
    {
		
		
		$session = $this->getRequest()->getSession();
		$session_id = $session->get('id');
		if( !$session_id ){
			return $this->redirect($this->generateUrl('user_welcome', array('back' => $_SERVER['REQUEST_URI'])));
		}
		
		
		$request = $this->getRequest();
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ApplicationTestBundle:Test')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Test entity.');
        }
	
	
		$ok = 0;
		$replies = explode( ',', $entity->getReplies() );
		$total = count( $replies );
		for( $i = 0; $i < $total; $i++ ){

			if( $request->get('question-' . ( $i + 1 ) ) == $replies[$i] ){
				$ok++;
			}
		}
		
		$result = ( $ok == $total );
		
		if( $result ){
			
			// contadores
			$query = $em->createQuery("SELECT COUNT(tu) as total FROM ApplicationTestBundle:TestUser tu WHERE tu.test_id = :test_id AND tu.user_id = :user_id");
			$query->setParameter('test_id', $id);
			$query->setParameter('user_id', $session_id);
			$total = current($query->getResult());
			if( !$total['total'] ){
			
				// guardar badge
				$badge = new \Application\TestBundle\Entity\TestUser;
				$badge->setTestId( $id );
				$badge->setUserId( $session_id );
				$badge->setDate( new \DateTime("now") );
				$em->persist($badge);
				$em->flush();
			
			}
			
			// redirigir
			return $this->redirect($this->generateUrl('test_show', array('id' => $id)) . '#win');
		}
		
		
        return array(
            'result' => $result,
			'total' => $total,
			'ok' => $ok,
			'entity' => $entity
        );
	

    }


    /**
     * Deletes a Test entity.
     *
     * @Route("/{id}/delete", name="test_delete")
     */
    public function deleteAction($id)
    {
		$em = $this->getDoctrine()->getEntityManager();
		$entity = $em->getRepository('ApplicationTestBundle:Test')->find($id);
		if (!$entity) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }
		
		$session = $this->getRequest()->getSession();
		$user_id = $session->get('id');
		$admin = $session->get('admin');
		
		if( ( $entity->getUserId() == $user_id ) || $admin ){

            $em->remove($entity);
            $em->flush();

			$url = $this->generateUrl('test');
		}else{
			$url = $this->generateUrl('test_show', array('id' => $entity->getId()));
			
		}
		return $this->redirect($url);
    }


}
