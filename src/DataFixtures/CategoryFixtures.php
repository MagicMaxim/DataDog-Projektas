<?php
/**
 * Created by PhpStorm.
 * User: Vartotojas
 * Date: 2019-05-16
 * Time: 16:41
 */

namespace App\DataFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Category;



class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $categories = ['Sports ', 'Movies', 'Entertainment', 'Art', 'Science',
            'Music', 'Comedy', 'IT'];

        for ($i = 0; $i < count($categories); $i++) {
            $category = new Category();
            $category->setName($categories[$i]);

            $manager->persist($category);
        }

        $manager->flush();
    }
}