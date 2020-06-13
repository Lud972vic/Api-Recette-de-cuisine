<?php

namespace App\Repository;

use App\Entity\Recette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Recette|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recette|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recette[]    findAll()
 * @method Recette[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecetteRepository extends ServiceEntityRepository
{
    private $manager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, Recette::class);
        $this->manager = $manager;
    }

    public function saveRecette($titre, $soustitre, $ingredients)
    {
        $newRecette = new Recette();

        $newRecette
            ->setTitre($titre)
            ->setSoustitre($soustitre)
            ->setIngredients($ingredients);

        $this->manager->persist($newRecette);
        $this->manager->flush();
    }

    public function updateRecette(Recette $recette): Recette
    {
        $this->manager->persist($recette);
        $this->manager->flush();

        return $recette;
    }

    public function removeRecette(Recette $recette)
    {
        $this->manager->remove($recette);
        $this->manager->flush();
    }

    public function findAll()
    {
        return $this->findBy(array(), array('id' => 'DESC'));
    }
}
