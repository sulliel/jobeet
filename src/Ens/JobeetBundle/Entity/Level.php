<?php

namespace Ens\JobeetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Level
 *
 * @ORM\Table(name="level")
 * @ORM\Entity(repositoryClass="Ens\JobeetBundle\Repository\LevelRepository")
 */
class Level
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, unique=true)
     * 
     * @Assert\Length(
     *      min = 3,
     *      max = 10,
     *      minMessage = "The description be at least {{ limit }} characters long",
     *      maxMessage = "The description cannot be longer than {{ limit }} characters"
     *      )
     */
    private $description;
    
    /**
     *
     * @var array @ORM\OneToMany(targetEntity="Job", mappedBy="levelId")
     */
    private $jobs;
    
    private $active_jobs;
    
    public function setActiveJobs($jobs) {
    	$this->active_jobs = $jobs;
    }
    public function getActiveJobs() {
    	return $this->active_jobs;
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Level
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Constructor
     */
    public function __construct() {
    	$this->jobs = new \Doctrine\Common\Collections\ArrayCollection ();
    	$this->category_affiliates = new \Doctrine\Common\Collections\ArrayCollection ();
    }
    
    /**
     * Add job
     *
     * @param \Ens\JobeetBundle\Entity\Job $job
     *
     * @return Category
     */
    public function addJob(\Ens\JobeetBundle\Entity\Job $job) {
    	$this->jobs [] = $job;
    	
    	return $this;
    }
    
    /**
     * Remove job
     *
     * @param \Ens\JobeetBundle\Entity\Job $job
     */
    public function removeJob(\Ens\JobeetBundle\Entity\Job $jobs) {
    	$this->jobs->removeElement ( $jobs );
    }
    
    /**
     * Get jobs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getJobs() {
    	return $this->jobs;
    }
    
    public static function getTypes()
    {
    	$em = $this->getDoctrine()->getManager();
    	$em->getRepository('EnsJobeetBundle:Job')->getJobsBylevel(25);
    	return array('High' => $em->getRepository('EnsJobeetBundle:Job')->getJobsBylevel(25), 'Medium' => $em->getRepository('EnsJobeetBundle:Job')->getJobsBylevel(26), 'Low' => $em->getRepository('EnsJobeetBundle:Job')->getJobsBylevel(27));
    }
    
    
}

