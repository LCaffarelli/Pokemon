<?php

namespace App\Controller;

use App\Entity\Pokemon;
use App\Form\PokemonFormType;
use App\Repository\PokemonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/pokemon/', name: 'pokemon_')]
class PokemonController extends AbstractController
{
    #[Route('list', name: 'list')]
    public function list(PokemonRepository $pokemonRepository): Response
    {
        $pokemons = $pokemonRepository->findAll();
        return $this->render('pokemon/list.html.twig', ['pokemons' => $pokemons]);
    }

    #[Route('capture/{id}', name: 'capture')]
    public function capture(Pokemon $pokemon, EntityManagerInterface $entityManager): RedirectResponse
    {

        $pokemon->setEstCapture(!$pokemon->isEstCapture());
        $entityManager->persist($pokemon);
        $entityManager->flush();
        return $this->redirectToRoute('pokemon_list');

    }

    #[Route('details/{id}', name: 'details')]
    public function details($id, PokemonRepository $pokemonRepository)
    {
        $pokemon = $pokemonRepository->find($id);
        return $this->render('pokemon/details.html.twig', ['pokemon' => $pokemon]);
    }

    #[Route('tri/{param}', name: 'tri')]
    public function tri($param, PokemonRepository $pokemonRepository): Response
    {
        if ($param == 'capture') {
            $pokemons = $pokemonRepository->triParCapture();
        } else {
            $pokemons = $pokemonRepository->triParNom();
        }
        return $this->render('pokemon/list.html.twig',
            compact("pokemons") // equivaut à ['pokemon'=>$pokemon]
        );
    }

    #[Route('create', name: 'create')]
    public function create(Request $request,SluggerInterface $slugger, EntityManagerInterface $entityManager)
    {
        $pokemon = new Pokemon();
        $pokemonForm = $this->createForm(PokemonFormType::class, $pokemon);
        $pokemonForm->handleRequest($request);

        if ($pokemonForm->isSubmitted() && $pokemonForm->isValid()) {
            $img = $pokemonForm->get('image')->getData();
            if ($img) {
                $originalFilename=pathinfo($img->getClientOriginalName(),PATHINFO_FILENAME);
                $safeFilename=$slugger->slug($originalFilename);
                $newFilename=$safeFilename.'-'.uniqid().'.'.$img->guessExtension();

                try{
                    $img->move($this->getParameter('images_directory'),$newFilename);

                }catch (FileException $e){

                }
                $pokemon->setImage($newFilename);
            }
            $entityManager->persist($pokemon);
            $entityManager->flush();
            $this->addFlash('success', 'Pokemon ajouté !');
            return $this->redirectToRoute('pokemon_details', ['id' => $pokemon->getId()]);
        }
        return $this->render('pokemon/create.html.twig', ['pokemonForm' => $pokemonForm->createView()]);
    }
}
