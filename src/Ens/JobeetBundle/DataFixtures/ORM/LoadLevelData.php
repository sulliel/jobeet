<?php
namespace Ens\JobeetBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Ens\JobeetBundle\Entity\Category;
use Ens\JobeetBundle\Entity\Level;

class LoadLevelData extends AbstractFixture implements OrderedFixtureInterface
{
	public function load(ObjectManager $em)
	{
		$high = new Level();
		$high->setDescription('High');
		
		$medium = new Level();
		$medium->setDescription('Medium');
		
		$low = new Level();
		$low->setDescription('Low');
		
		$em->persist($high);
		$em->persist($medium);
		$em->persist($low);
		
		$em->flush();
		
		$this->addReference('high', $high);
		$this->addReference('low', $low);
		$this->addReference('medium', $medium);
		
	}
	
	public function getOrder()
	{
		return 2; // the order in which fixtures will be loaded
	}
}