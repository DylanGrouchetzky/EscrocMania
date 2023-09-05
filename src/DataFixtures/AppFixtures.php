<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Frontend\Entity\Row;
use App\Frontend\Entity\Tag;
use App\Frontend\Entity\Order;
use App\Frontend\Entity\Article;
use App\Frontend\Entity\Comment;
use App\Frontend\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        
        

        $manager->flush();
    }
}
