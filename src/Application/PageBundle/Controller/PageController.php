<?php

namespace Application\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * Page controller.
 *
 * @Route("/page")
 */
class PageController extends Controller
{
    /**
     * faq
     *
     * @Route("/faq", name="page_faq")
     * @Template()
     */
    public function faqAction()
    {
		return array();
	}
	
    /**
     * about
     *
     * @Route("/about", name="page_about")
     * @Template()
     */
    public function aboutAction()
    {
		return array();
	}
	
    /**
     * opensource
     *
     * @Route("/opensource", name="page_opensource")
     * @Template()
     */
    public function opensourceAction()
    {
		return array();
	}
	
    /**
     * thanks
     *
     * @Route("/thanks", name="page_thanks")
     * @Template()
     */
    public function thanksAction()
    {
		return array();
	}
	
    /**
     * success
     *
     * @Route("/success", name="page_success")
     * @Template()
     */
    public function successAction()
    {
		return array();
	}
}