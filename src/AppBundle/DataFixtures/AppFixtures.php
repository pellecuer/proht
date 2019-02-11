<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Agent;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;






class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    
    
    public function load(ObjectManager $manager)
    {
        // create 20 products! Bam!
        // Liste des choses à ajouter       
        $nni = ['1', '2', '3', '4'];
        $name = ['a1', 'b2', 'c3', 'd4'];
        
        for ($i = 0; $i < count($teamId); $i++) {
            $agent = new Agent();
            
            $agent->setNni($nni[$i]);
            $agent->setName($name[$i]);
            
            //A compléter
                        
            $password = $this->encoder->encodePassword($agent, 'pass_1234');
            $agent->setPassword($password);
            
            
            $manager->persist($agent);
        }

        $manager->flush();
    }
}
