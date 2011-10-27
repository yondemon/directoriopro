<?php

namespace Application\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Application\ProjectBundle\Entity\Project;
use Application\ProjectBundle\Entity\CommentProject;
use Application\ProjectBundle\Form\ProjectType;
use Application\ProjectBundle\Form\CommentType;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\View\DefaultView;
use Pagerfanta\Adapter\DoctrineORMAdapter;

/**
 * Project controller.
 *
 * @Route("/project")
 */
class ProjectController extends Controller
{
    /**
     * Lists all Project entities.
     *
     * @Route("/", name="project")
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
           ->add('from', 'ApplicationProjectBundle:Project p')
           //->add('where', 'p.type = 0')
           ->add('orderBy', 'p.id DESC');
        
        // type?
        $type = $request->query->get('t');
        if( !$type ) $type = 0;
        else if( !in_array( $type, array( 0, 1, 2 ) ) ) return $this->redirect($this->generateUrl('project'));

        if( $type ){
            $query->andWhere('p.type = :type')->setParameter('type', $type);
        }else{
            $query->andWhere('p.type = 0');
        }

        // category_id?
        $category_id = $request->query->get('c');

        if( $category_id ) $query->andWhere('p.category_id = :category_id')->setParameter('category_id', $category_id);
		else $category_id = 0;


        
        $adapter = new DoctrineORMAdapter($query);

        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(10); // 10 by default
        $maxPerPage = $pagerfanta->getMaxPerPage();

        $pagerfanta->setCurrentPage($page); // 1 by default
        $entities = $pagerfanta->getCurrentPageResults();
        $routeGenerator = function($page, $category_id, $type) {
            $url = '?page='.$page;
            if( $category_id ) $url .= '&c=' . $category_id;
			if( $type ) $url .= '&t=' . $type;
            return $url;
        };

        $view = new DefaultView();
        $html = $view->render($pagerfanta, $routeGenerator, array('category_id' => (int)$category_id, 'type' => (int)$type));
        



        $twig = $this->container->get('twig'); 
        $twig->addExtension(new \Twig_Extensions_Extension_Text);


		switch( $type ){
			case 0:
				$page_title = 'Ideas';
				break;
			
			case 1:
				$page_title = 'Beta';
				break;
		
			case 2:
				$page_title = 'Startup';
				break;
			
			default:
				throw $this->createNotFoundException('Unable to find Category entity.');
				break;
			
		}

        return array('pager' => $html, 'entities' => $entities, 'type' => $type, 'page_title' => $page_title, 'category_id' => $category_id);
    }

    /**
     * Finds and displays a Project entity.
     *
     * @Route("/{id}/show", name="project_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ApplicationProjectBundle:Project')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
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

		$youtube_url = $entity->getYoutubeUrl();
		$youtube_id = false;
		if( $youtube_url ){
			preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $youtube_url, $matches);
			if( $matches ){
				$youtube_id = $matches[0];
			}
		}
		
		// contadores
		$query = $em->createQuery("SELECT COUNT(c) as total FROM ApplicationProjectBundle:CommentProject c WHERE c.to_id = :id AND c.type = 0");
		$query->setParameter('id', $id);
		$total = current($query->getResult());
		$total_ideas = $total['total'];
		
		$query = $em->createQuery("SELECT COUNT(c) as total FROM ApplicationProjectBundle:CommentProject c WHERE c.to_id = :id AND c.type = 1");
		$query->setParameter('id', $id);
		$total = current($query->getResult());
		$total_bugs = $total['total'];

        return array(
            'entity'      => $entity,
            'user'        => $user,
			'youtube_id'  => $youtube_id,
			'total_ideas' => $total_ideas,
			'total_bugs'  => $total_bugs
            );
    }

    /**
     * Displays a form to create a new Project entity.
     *
     * @Route("/new", name="project_new")
     * @Template()
     */
    public function newAction()
    {


        $entity = new Project();

        $request = $this->getRequest();
        $type = $request->query->get('type');
        if( !$type ) $type = 0;
        $entity->setType($type);

        
        $form   = $this->createForm(new ProjectType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new Project entity.
     *
     * @Route("/create", name="project_create")
     * @Method("post")
     * @Template("ApplicationProjectBundle:Project:new.html.twig")
     */
    public function createAction()
    {
        $entity  = new Project();

        // rellenar campos que faltan
        $session = $this->getRequest()->getSession();
        $user_id = $session->get('id');
        $entity->setUserId( $user_id );
        $entity->setDate( new \DateTime("now") );
        $entity->setVisits(1);


        $request = $this->getRequest();
        $form    = $this->createForm(new ProjectType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('project_show', array('id' => $entity->getId())));
            
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Project entity.
     *
     * @Route("/{id}/edit", name="project_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ApplicationProjectBundle:Project')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }


        $session = $this->getRequest()->getSession();
        $user_id = $session->get('id');
        $admin = $session->get('admin');
        
        if( ( $entity->getUserId() == $user_id ) || $admin ){
            
            $editForm = $this->createForm(new ProjectType(), $entity);
        
            return array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
            );

        }else{
            $url = $this->generateUrl('project_show', array('id' => $entity->getId()));
            return $this->redirect($url);
        }




    }

    /**
     * Edits an existing Project entity.
     *
     * @Route("/{id}/update", name="project_update")
     * @Method("post")
     * @Template("ApplicationProjectBundle:Project:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ApplicationProjectBundle:Project')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }


        $session = $this->getRequest()->getSession();
        $user_id = $session->get('id');
        $admin = $session->get('admin');
        
        if( ( $entity->getUserId() == $user_id ) || $admin ){
            
            $editForm   = $this->createForm(new ProjectType(), $entity);
            

            $request = $this->getRequest();

            $editForm->bindRequest($request);

            if ($editForm->isValid()) {
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('project_show', array('id' => $id)));
            }

            return array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
            );

        }else{
            $url = $this->generateUrl('project_show', array('id' => $entity->getId()));
            return $this->redirect($url);
        }
    }


    /**
     * Deletes a Project entity.
     *
     * @Route("/{id}/delete", name="project_delete")
     */
    public function deleteAction($id)
    {
	
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('ApplicationProjectBundle:Project')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }
        
        $session = $this->getRequest()->getSession();
        $user_id = $session->get('id');
        $admin = $session->get('admin');
        
        if( ( $entity->getUserId() == $user_id ) || $admin ){

            $em->remove($entity);
            $em->flush();

            $url = $this->generateUrl('project');
        }else{
            $url = $this->generateUrl('project_show', array('id' => $entity->getId()));
            
        }
        return $this->redirect($url);
    }

    /**
     * Comment form
     *
     * @Route("/{id}/comments/new", name="project_comments_new")
     * @Template("ApplicationProjectBundle:Project:comments_new.html.twig")
     */
    public function commentsNewAction($id)
    {
		// esta logueado?
		$session = $this->getRequest()->getSession();
		$session_id = $session->get('id');
		if( !$session_id ){
			return $this->redirect($this->generateUrl('user_welcome', array('back' => $_SERVER['REQUEST_URI'])));
		}


		// existe proyecto?
        $em = $this->getDoctrine()->getEntityManager();
		$project = $em->getRepository('ApplicationProjectBundle:Project')->find($id);
        if (!$project) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

		$entity = new CommentProject();
		$form = $this->createForm(new CommentType(), $entity);

		$request = $this->getRequest();
		if ($request->getMethod() == 'POST') {
	        $form->bindRequest($request);

	        if ($form->isValid()) {
		
		

				
				
				
				$entity->setFromId( $session_id );
				$entity->setToId( $id );
				$entity->setDate( new \DateTime("now") );
				$em->persist($entity);
				$em->flush();
				
				// guardar total recomendaciones
				$query = $em->createQuery("SELECT COUNT(c) as total FROM ApplicationProjectBundle:CommentProject c WHERE c.to_id = :id");
				$query->setParameter('id', $id);
				$votes = current($query->getResult());

				$entity->setVotes( $votes['total'] );
				$em->persist($entity);
				$em->flush();
				
				

				$url = $this->generateUrl('project_comment_show', array('project_id' => $id, 'comment_id' => $entity->getId() ));
				return $this->redirect($url);
				
	        }
	    }

		return array(
			'form' => $form->createView(),
			'project' => $project
			);

    }


    /**
     * User recommendations
     *
     * @Route("/{id}/comments", name="project_comments")
     * @Template()
     */
    public function commentsAction($id)
    {
		// existe proyecto?
        $em = $this->getDoctrine()->getEntityManager();
		$project = $em->getRepository('ApplicationProjectBundle:Project')->find($id);
        if (!$project) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
	
		$query = $em->createQuery("SELECT u.name, u.category_id, c.id, c.from_id, c.body, c.type, c.date FROM ApplicationUserBundle:User u, ApplicationProjectBundle:CommentProject c WHERE u.id = c.from_id AND c.to_id = :id ORDER BY c.id DESC");
		$query->setParameter('id', $id);
		$comments = $query->getResult();
		

		$total = count($comments);
	
		return array(
			'project' => $project,
			'comments' => $comments,
			'total' => $total
			);
	}
	

    /**
     * User recommendation
     *
     * @Route("/{project_id}/comments/{comment_id}", name="project_comment_show")
     * @Template("ApplicationProjectBundle:Project:comments.html.twig")
     */
    public function commentsShowAction($project_id, $comment_id)
    {
        $em = $this->getDoctrine()->getEntityManager();

		// existe proyecto?
		$project = $em->getRepository('ApplicationProjectBundle:Project')->find($project_id);
        if (!$project) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

		// existe comentario?
		$query = $em->createQuery("SELECT u.name, u.category_id, c.id, c.from_id, c.body, c.type, c.date FROM ApplicationUserBundle:User u, ApplicationProjectBundle:CommentProject c WHERE u.id = c.from_id AND c.to_id = :to_id AND c.id = :id ORDER BY c.id DESC");
		$query->setParameters(array(
			'id' => $comment_id,
			'to_id' => $project_id
		));
		$comments = $query->getResult();
        if (!$comments) {
            throw $this->createNotFoundException('Unable to find Comment entity.');
        }
	
		$query = "SELECT COUNT(c.id) AS total FROM CommentProject c WHERE c.to_id = " . $project_id;
		$db = $this->get('database_connection');
		$result = $db->query($query)->fetch();
		$total = $result['total'];
	
		return array(
			'project' => $project,
			'comments' => $comments,
			'total' => $total
			);
	}

}
