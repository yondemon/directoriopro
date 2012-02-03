<?php

namespace Application\CityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Application\UserBundle\Entity\City;

/**
 * City controller.
 *
 * @Route("/city")
 */
class CityController extends Controller
{
    /**
     * @Route("/", name="city")
     * @Template()
     */
    public function indexAction()
    {
		$em = $this->getDoctrine()->getEntityManager();
		$qb = $em->createQueryBuilder();
		$qb->add('select', 'c')
		   ->add('from', 'ApplicationCityBundle:City c')
    	   ->add('where','c.id IN(3117735,3128760,2509954)') //mad,bcn,val
		   //->add('where',"c.code = 'ES'")
		   ->add('orderBy', 'c.name ASC');
		
		$query = $qb->getQuery();
		$cities = $query->getResult();

        return array('cities' => $cities);
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}/show", name="city_show")
     * @Template()
     */
    public function showAction($id)
    {
		// esta logueado?
		/*
		$session = $this->getRequest()->getSession();
		$id = $session->get('id');
		if( !$id ){
			return $this->redirect('/');
		}*/
		
        $em = $this->getDoctrine()->getEntityManager();
		
		// usuario
		//$user = $em->getRepository('ApplicationUserBundle:User')->find($id);
		
		// ciudad
		//$city_id = $user->getCityId();
		$city = $em->getRepository('ApplicationCityBundle:City')->find($id);
		
		// pais
		//$country_id = $user->getCountryId();
		//$country = $em->getRepository('ApplicationUserBundle:Country')->find($country_id);
		
		// usuarios
		$qb = $em->createQueryBuilder();
		$qb->add('select', 'u')
		   ->add('from', 'ApplicationUserBundle:User u')
		   //->andWhere('u.id != :id')->setParameter('id', $id)
		   ->andWhere('u.city_id != :city_id')->setParameter('city_id', $id)
		   ->setMaxResults(6);
		   //->add('orderBy', 'RAND()');
		$query = $qb->getQuery();
		$users = $query->getResult();

		// ideas
		$qb = $em->createQueryBuilder();
		$qb->add('select', 'p')
		   ->add('from', 'ApplicationProjectBundle:Project p, ApplicationUserBundle:User u')
		   ->andWhere('p.user_id = u.id')
		   ->andWhere('p.type = :type')->setParameter('type', 0)
		   //->andWhere('p.user_id != :id')->setParameter('id', $id)
		   ->andWhere('u.city_id = :city_id')->setParameter('city_id', $id)
		   ->setMaxResults(10);
		   //->add('orderBy', 'RAND()');
		$query = $qb->getQuery();
		$ideas = $query->getResult();

	 	//$twig = $this->container->get('twig'); 
	    //$twig->addExtension(new \Twig_Extensions_Extension_Text);
		
		return array('users' => $users, 'ideas' => $ideas, 'city' => $city);//, 'country' => $country

	}
}
