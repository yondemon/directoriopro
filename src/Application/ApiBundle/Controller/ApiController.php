<?php

namespace Application\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

use Application\UserBundle\Entity\User;
use Application\EventBundle\Entity\Event;
use Application\EventBundle\Entity\EventUser;
use Application\AnunciosBundle\Entity\Post;

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
			
			$url = $user->getUrl();
			if( !$url ) $url = $this->generateUrl('user_show', array('id' => $user->getId()), true);
			
			$profile = array(
				'name' => $user->getName(),
				'email' => $user->getEmail(),
				'url' => $url,
				'location' => $user->getLocation(),
				'phone' => $user->getPhone(),
			);
			$response = array('result' => 'ok', 'profile' => $profile);

		}else{
			$response = array('result' => 'ko');
		}	
		return new Response('jsontest('.json_encode($response).')');
	}
	
	
    /**
     * Lists all Events entities.
     *
     * @Route("/events", name="api_events")
     */
    public function eventsAction()
    {
	
		$request = $this->getRequest();
		$callback = $request->query->get('callback');
	
		$em = $this->getDoctrine()->getEntityManager();

		$qb = $em->createQueryBuilder()
		   ->add('select', 'e')
		   ->add('from', 'ApplicationEventBundle:Event e')
		   ->andWhere('e.date_start > :date')->setParameter('date', date('Y-m-d 00:00:00') )
		   ->add('orderBy', 'e.date_start ASC')
		   ->setMaxResults(20);

		$entities = $qb->getQuery()->getResult();
		
		$events = array();
		foreach( $entities as $entity ){
			$events[] = array(
				'id' => $entity->getId(),
				'title' => $entity->getTitle() . ' - ' . $entity->getPrettyDate('%e %B'),
				'text' => nl2br( $entity->getBody() ),
				'url' => $this->get('router')->generate('event_show', array('id' => $entity->getId(), 'slug' => $entity->getSlug()), true),
				'users' => $entity->getUsers()
			);
		}

	 	$twig = $this->container->get('twig'); 
	    $twig->addExtension(new \Twig_Extensions_Extension_Text);
		
		return new Response($callback.'('.json_encode($events).')');
    }
	
    /**
     * Lists all Jobs entities.
     *
     * @Route("/jobs", name="api_jobs")
     */
    public function jobsAction()
    {
	
		$request = $this->getRequest();
		$callback = $request->query->get('callback');
	
		$em = $this->getDoctrine()->getEntityManager();

		$qb = $em->createQueryBuilder()
		   ->add('select', 'p')
		   ->add('from', 'ApplicationAnunciosBundle:Post p')
		   ->add('where', 'p.visible = 1')
		   ->add('orderBy', 'p.id DESC')
		   ->setMaxResults(20);

		$entities = $qb->getQuery()->getResult();
		
		$jobs = array();
		foreach( $entities as $entity ){
			$jobs[] = array(
				'id' => $entity->getId(),
				'title' => $entity->getTitle(),
				'text' => nl2br( $entity->getBody() ),
				'url' => $this->get('router')->generate('post_show', array('id' => $entity->getId(), 'slug' => $entity->getSlug()), true)
			);
		}

	 	$twig = $this->container->get('twig'); 
	    $twig->addExtension(new \Twig_Extensions_Extension_Text);
		
		return new Response($callback.'('.json_encode($jobs).')');
    }
	
    /**
     * Lists all Users from event.
     *
     * @Route("/event_users/{id}", name="api_events_users")
     */
    public function eventusersAction($id)
    {
		$categories = array("Todos", "Programador frontend", "Programador backend", "Programador apps móvil", "Blogger", "Community manager", "Marketing", "SEO", "Diseñador", "Usabilidad", "Sysadmin", "Traductor", "Betatester", "Otros", "Maquetador");
	
		$request = $this->getRequest();
		$callback = $request->query->get('callback');
		
		$em = $this->getDoctrine()->getEntityManager();
		$qb = $em->createQueryBuilder();
		$qb->add('select', 'u')
		   ->add('from', 'ApplicationUserBundle:User u, ApplicationEventBundle:EventUser eu')
		   ->andWhere('u.id = eu.user_id')
		   ->andWhere('eu.event_id = :id')->setParameter('id', $id);
		$entities = $qb->getQuery()->getResult();
		$users = array();
		foreach( $entities as $entity ){
			$users[] = array(
				'name' => $entity->getName(),
				//'text' => $entity->getBody(),
				'avatar' => $entity->getAvatar(),
				'type' => $categories[$entity->getCategoryId()]
				
			);
		}
		return new Response($callback.'('.json_encode($users).')');
	}
}