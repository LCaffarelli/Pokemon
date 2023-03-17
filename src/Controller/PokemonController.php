<?php

namespace App\Controller;

use App\Repository\PokemonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/pokemon/', name: 'pokemon_')]
class PokemonController extends AbstractController
{
    #[Route('list', name: 'list')]
    public function list(PokemonRepository $pokemonRepository): Response
    {
        $pokemons=$pokemonRepository->findAll();
        return $this->render('pokemon/list.html.twig',['pokemons'=>$pokemons]);
    }

    #[Route('capture', name: 'capture')]
    public function capture():Response{
        return $this->render('pokemon/capture.html.twig');
    }

    #[Route('details/{id}', name: 'details')]
    public function details($id,PokemonRepository $pokemonRepository){
        $pokemon=$pokemonRepository->find($id);
    return $this->render('pokemon/details.html.twig',['pokemon'=>$pokemon]);
    }
}
