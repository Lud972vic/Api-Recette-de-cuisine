<?php

namespace App\Controller;

use App\Entity\Condiment;
use App\Entity\Recette;
use App\Repository\RecetteRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecetteController extends AbstractController
{
    private $recetteController;

    public function __construct(RecetteRepository $recetteRepository)
    {
        $this->recetteRepository = $recetteRepository;
    }

    /**
     * @Route("/recettes/", name="add_recette/", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $titre = $data['titre'];
        $soustitre = $data['soustitre'];
        $ingredients = $data['ingredients'];

        if (empty($titre)) {
            return new JsonResponse(['status' => 'Le titre est obligatoire'], Response::HTTP_CREATED);
        }
        $this->recetteRepository->saveRecette($titre, $soustitre, $ingredients);
        return new JsonResponse(['status' => 'Recette disponible'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/recettes/{id}", name="get_one_recette", methods={"GET"})
     */
    public function get($id): JsonResponse
    {
        $recette = $this->recetteRepository->findOneBy(['id' => $id]);

        $data = [
            'id' => $recette->getId(),
            'titre' => $recette->getTitre(),
            'soustitre' => $recette->getSoustitre(),
            'ingredients' => $recette->getIngredients()
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/recettes", name="get_all", methods={"GET"})
     */
    public function getAll(): JsonResponse
    {
        $recettes = $this->recetteRepository->findAll();
        $data = [];

        foreach ($recettes as $recette) {
            $data[] = [
                'id' => $recette->getId(),
                'titre' => $recette->getTitre(),
                'soustitre' => $recette->getSoustitre(),
                'ingredinets' => $recette->getIngredients()
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/recettes_form", name="get_all_recettes", methods={"GET"})
     */
    public function getAllRecette(Request $request, PaginatorInterface $paginator)
    {
        $donnees = $this->recetteRepository->findAll();
        $recettes = $paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1),
            4
        );

        return $this->render('crud/recette.html.twig', [
            'recettes' => $recettes,
        ]);
    }

    /**
     * @Route("/recette_update/{id}", name="update_recette", methods={"PUT"})
     */
    public function update($id, Request $request): JsonResponse
    {
        $recette = $this->recetteRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);

        empty($data['titre']) ? true : $recette->setTitre($data['titre']);
        empty($data['soustitre']) ? true : $recette->setSoustitre($data['soustitre']);
        empty($data['ingredients']) ? true : $recette->setIngredients($data['ingredients']);

        $updatedRecette = $this->recetteRepository->updateRecette($recette);

        return new JsonResponse($updatedRecette->toArray(), Response::HTTP_OK);
    }

    /**
     * @Route("/recette_delete/{id}", name="delete_recette", methods={"GET","DELETE"})
     */
    public function delete(Request $request, $id): JsonResponse
    {
        $recette = $this->recetteRepository->findOneBy(['id' => $id]);
        $this->recetteRepository->removeRecette($recette);

        return new JsonResponse(['status' => 'Recette supprimee'], Response::HTTP_OK);
    }

    /**
     * @Route("/recette_delfiche/{id}", name="delete_recettefiche", methods={"GET","DELETE"})
     */
    public function delfiche(Request $request, $id)
    {
        $recette = $this->recetteRepository->findOneBy(['id' => $id]);
        $this->recetteRepository->removeRecette($recette);

        $this->addFlash('success', 'Recette supprimée !');
        return $this->redirect('/');
    }

    /**
     * @Route("/recette/{id}", name="recette_show",methods={"GET"})
     */
    public function show($id)
    {
        $recette = $this->recetteRepository->findOneBy(['id' => $id]);
        $condiment = $recette->getCondiments()->getValues();

        return $this->render('crud/show.html.twig', array('recette' => $recette, 'condiment' => $condiment));
    }

    /**
     * @Route("/recette/edit/{id}", name="recette_edit")
     * Method({"GET", "POST"})
     */
    public function edit(Request $request, $id)
    {
        $recette = $this->recetteRepository->findOneBy(['id' => $id]);

        $form = $this->createFormBuilder($recette)
            ->add('titre', TextType::class, array('attr' => array(
                'required' => true,
                'class' => 'form-control'
            )))
            ->add('soustitre', TextType::class, array(
                'required' => false,
                'attr' => array('class' => 'form-control')
            ))
            ->add('save', SubmitType ::class, array(
                'label' => 'Mettre à jour',
                'attr' => array('class' => 'btn btn-outline-primary mt-3')
            ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Recette actualisée !');
            return $this->redirect('/');
        }

        return $this->render('crud/edit.html.twig', array(
            'editForm' => $form->createView()
        ));
    }

    /**
     * @Route("/recette_add", name="recette_add")
     * Method({"GET", "POST"})
     */
    public function new(Request $request)
    {
        $recette = new Recette();

        $form = $this->createFormBuilder($recette)
            ->add('titre', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('soustitre', TextType::class, array(
                'required' => false,
                'attr' => array('class' => 'form-control')
            ))
            ->add('Enregistrer', SubmitType::class, array(
                'label' => 'Ajouter',
                'attr' => array('class' => 'btn btn-outline-primary mt-3')
            ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recette = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($recette);
            $entityManager->flush();

            $this->addFlash('success', 'Recette ajoutée !');
            return $this->redirect('/');
        }

        return $this->render('crud/add.html.twig', array(
            'newForm' => $form->createView()
        ));

    }

    /**
     * @Route("/ingredient_add/{id}", name="ingredient_add")
     * Method({"GET", "POST"})
     */
    public function newIngredient(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $ingredient = new Condiment();

        $recette = $entityManager->getRepository(Recette::class)->findOneBy(array('id' => $id));

        $form = $this->createFormBuilder($ingredient)
            ->add('libelle', TextType::class, array('attr' => array('class' => 'form-control', 'placeholder' => 'Ajouter vos condiments et Ajouter')))
            ->add('Enregistrer', SubmitType::class, array(
                'label' => 'Ajouter',
                'attr' => array('class' => 'btn btn-outline-primary mt-3')
            ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->all();
            $libelle = $data["form"]["libelle"];
            $ingredient->setLibelle($libelle);
            $ingredient->setRecette($recette);
            $entityManager->persist($ingredient);
            $entityManager->flush();

            $this->addFlash('success', 'Ingrédient ajouté !');
            return $this->redirect('/');
        }

        return $this->render('crud/addingredient.html.twig', array(
            'ingredientForm' => $form->createView(),
            'recette' => $recette
        ));
    }
}
