<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use AppBundle\Form\Type\ArticleType;


class ArticleController extends Controller
{
    /**
     * @Rest\Get("/articles")
     */
    public function getArticlesAction()
    {
        $article = new Article();
		$em = $this->getDoctrine()->getManager();
		$restresult = $this->getDoctrine()->getRepository('AppBundle:Article')->findAll();


        $data = $this->get('jms_serializer')->serialize($restresult, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
	
	 /**
	 * @Rest\Get("/articles/{id}")
	 */
	 public function getArticleAction($id)
	 {
	   $singleresult = $this->getDoctrine()->getRepository('AppBundle:Article')->find($id);
	   if ($singleresult === null) {
	   return new View("user not found", Response::HTTP_NOT_FOUND);
	   }
		$data = $this->get('jms_serializer')->serialize($singleresult, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
	 }
	
	/**
    * @Rest\Post("/articles")
    */
    public function postArticlesAction(Request $request)
    {
        $article = new Article();

		$article->setTitle($request->get('title'));
		$category = $this->getDoctrine()->getRepository('AppBundle:Category')->find($request->get('category'));
		$article->setCategory($category);
		$article->setContent($request->get('content'));
		
		// On récupère le service validator
		$validator = $this->get('validator');
			
		// On déclenche la validation sur notre object
		$listErrors = $validator->validate($article);

		// Si le tableau n'est pas vide, on affiche les erreurs
		if(count($listErrors) > 0) {
		  return new Response("error", Response::HTTP_CREATED);
		} else {
			$em = $this->getDoctrine()->getManager();
			$em->persist($article);
			$em->flush();

			return new Response('', Response::HTTP_CREATED);		
		}

		//var_dump($request->get('title'));die;
        //$article = $this->get('jms_serializer')->deserialize($data, 'AppBundle\Entity\Article', 'json');
		

        

    }
	/**
    * @Rest\Put("/articles/{id}")
    */
    public function updateArticlesAction($id,Request $request)
    {
		$article = new Article();
		$article = $this->getDoctrine()->getRepository('AppBundle:Article')->find($id);
		$title = $request->get('title');
		$content = $request->get('content');
		$category = $this->getDoctrine()->getRepository('AppBundle:Category')->find($request->get('category'));
		$em = $this->getDoctrine()->getManager();
		$article->setTitle($title);
		$article->setCategory($category);
		$article->setContent($content);

        
        $em->persist($article);
        $em->flush();

        return new Response('', Response::HTTP_CREATED);

    }
	/**
	* @Rest\Delete("/articles/{id}")
	*/
	 public function deleteAction($id)
	 {
		$article = new Article;
		$em = $this->getDoctrine()->getManager();
		$article = $this->getDoctrine()->getRepository('AppBundle:Article')->find($id);
		
		$em->remove($article);
		$em->flush();
		return new View("deleted successfully", Response::HTTP_OK);
	 }
}

