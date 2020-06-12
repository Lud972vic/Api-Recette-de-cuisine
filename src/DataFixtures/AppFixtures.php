<?php

namespace App\DataFixtures;

use App\Entity\Condiment;
use App\Entity\Recette;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory:: create('fr_FR');

        for ($j = 0; $j < 5; $j++) {

            $num = $j + 1;
            $recette = new Recette();
            $recette->setTitre('Recette n° ' . $num . " : " . $faker->sentence($nbWords = 1, $variableNbWords = true));
            $recette->setSoustitre('Sous-titre : ' . $faker->sentence($nbWords = 3, $variableNbWords = true));
            $recette->setIngredients("");

            $manager->persist($recette);

            for ($i = 0; $i <= mt_rand(2, 4); $i++) {
                $condiment = new Condiment();
                $condiment->setLibelle($faker->sentence($nbWords = 3, $variableNbWords = true));
                $condiment->setRecette($recette);
                $manager->persist($condiment);
            }
        }

        $manager->flush();
    }
}
