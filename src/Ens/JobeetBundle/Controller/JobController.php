<?php

namespace Ens\JobeetBundle\Controller;

use Ens\JobeetBundle\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


/**
 * Job controller.
 *
 * @Route("/job")
 */
class JobController extends Controller {
	/**
	 * Lists all job entities.
	 *
	 * @Route("/", name="ens_job")
	 * 
	 * @method ("GET")
	 */
	public function indexAction() {
		$em = $this->getDoctrine ()->getManager ();
		
		$categories = $em->getRepository ( 'EnsJobeetBundle:Category' )->getWithJobs ();
		
		foreach ( $categories as $category ) {
			$category->setActiveJobs ( $em->getRepository ( 'EnsJobeetBundle:Job' )->getActiveJobs ( $category->getId (), $this->container->getParameter ( 'max_jobs_on_homepage' ) ) );
			
			$category->setMoreJobs($em->getRepository('EnsJobeetBundle:Job')->countActiveJobs($category->getId()) - $this->container->getParameter('max_jobs_on_homepage'));
		}
		
		return $this->render ( 'EnsJobeetBundle:Job:index.html.twig', array (
				'categories' => $categories 
		) );
	}
	
	/**
	 * Creates a new job entity.
	 *
	 * @Route("/new", name="ens_job_new")
	 * 
	 * @method ({"GET", "POST"})
	 */
	public function newAction(Request $request) {
		$job = new Job ();
		$job->setType('full-time');
		$form = $this->createForm ( 'Ens\JobeetBundle\Form\JobType', $job );
		$form->handleRequest ( $request );
		
		if ($form->isSubmitted () && $form->isValid ()) {
			$em = $this->getDoctrine ()->getManager ();
			$em->persist ( $job );
			$em->flush ( $job );
			
			return $this->redirect($this->generateUrl('ens_job_preview', array(
					'company' => $job->getCompanySlug(),
					'location' => $job->getLocationSlug(),
					'token' => $job->getToken(),
					'position' => $job->getPositionSlug()
			)));
		}
		
		return $this->render ( 'EnsJobeetBundle:Job:new.html.twig', array (
				'job' => $job,
				'form' => $form->createView () 
		) );
	}
	
	/**
	 * Finds and displays a job entity.
	 *
	 * @Route("/{company}/{location}/{id}/{position}", name="ens_job_show", requirements={"id": "\d+"})
	 * 
	 * @method ("GET")
	 */
	public function showAction($id) {
		$em = $this->getDoctrine ()->getManager ();
		$job = $em->getRepository ( 'EnsJobeetBundle:Job' )->getActiveJob ( $id );
	
		
		if (! $job) {
			throw $this->createNotFoundException ( 'No job found for id ' . $id );
		}
		
		$deleteForm = $this->createDeleteForm ( $job );
		
		return $this->render ( 'EnsJobeetBundle:Job:show.html.twig', array (
				'job' => $job,
				'delete_form' => $deleteForm->createView () 
		) );
	}
	
	/**
	 * Displays a form to edit an existing job entity.
	 *
	 * @Route("/{token}/edit", name="ens_job_edit")
	 * 
	 * @method ({"GET", "POST"})
	 */
	public function editAction(Request $request, Job $job) {
		$deleteForm = $this->createDeleteForm ( $job );
		$editForm = $this->createForm ( 'Ens\JobeetBundle\Form\JobType', $job );
		$editForm->handleRequest ( $request );
		
		if ($editForm->isSubmitted () && $editForm->isValid ()) {
			$this->getDoctrine ()->getManager ()->flush ();
			
			return $this->redirect($this->generateUrl('ens_job_preview', array(
					'company' => $job->getCompanySlug(),
					'location' => $job->getLocationSlug(),
					'token' => $job->getToken(),
					'position' => $job->getPositionSlug()
			)));
		}
		
		return $this->render ( 'EnsJobeetBundle:Job:edit.html.twig', array (
				'job' => $job,
				'edit_form' => $editForm->createView (),
				'delete_form' => $deleteForm->createView () 
		) );
	}
	
	/**
	 * Deletes a job entity.
	 *
	 * @Route("/{token}", name="ens_job_delete")
	 * 
	 * @method ("DELETE")
	 */
	public function deleteAction(Request $request, Job $job) {
		$form = $this->createDeleteForm ( $job );
		$form->handleRequest ( $request );
		
		if ($form->isSubmitted () && $form->isValid ()) {
			$em = $this->getDoctrine ()->getManager ();
			$em->remove ( $job );
			$em->flush ( $job );
		}
		
		return $this->redirectToRoute ( 'ens_job' );
	}
	
	/**
	 * Creates a form to delete a job entity.
	 *
	 * @param Job $job
	 *        	The job entity
	 *        	
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createDeleteForm(Job $job) {
		return $this->createFormBuilder ()->setAction ( $this->generateUrl ( 'ens_job_delete', array (
				'token' => $job->getToken() 
		) ) )->setMethod ( 'DELETE' )->getForm ();
	}
	/**
	 * displays a job preview entity.
	 *
	 * @Route("/{company}/{location}/{token}/{position}", name="ens_job_preview")
	 *
	 * 
	 */
	public function previewAction($token)
	{
		$em = $this->getDoctrine()->getManager();
		
		$job = $em->getRepository('EnsJobeetBundle:Job')->findOneByToken($token);
		
		if (!$job) {
			throw $this->createNotFoundException('Unable to find Job entity.');
		}
		
		$deleteForm = $this->createDeleteForm($job);
		
		return $this->render('EnsJobeetBundle:Job:show.html.twig', array(
				'job'      => $job,
				'delete_form' => $deleteForm->createView(),
		));
	}
}
