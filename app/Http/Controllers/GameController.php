<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use Illuminate\Support\Facades\Http;

class GameController extends Controller
{   

    public function store()
    {
        set_time_limit(300);
        $nextPage = '&page=';
        $pageNumber = 1;
        while (true) {
            $responseGames = Http::get('https://api.rawg.io/api/games?key=5ba552549cf8448b967a9d1788b45491' . $nextPage . $pageNumber . '&tags=jrpg');
            $pageNumber++;
            foreach ($responseGames->json()['results'] as $jogo) {

                $tags = [];
                $genres = [];
                $platforms = [];
                $developers = [];


                if($jogo['platforms'] == null){
                    $jogo['platforms'] = [];
                }     

                foreach ($jogo['tags'] as $tag) {
                    $tags[] = $tag['name'];
                }

                foreach ($jogo['genres'] as $genre) {
                    $genres[] = $genre['name'];
                }
          
                foreach ($jogo['platforms'] as $platform) {
                    $platforms[] = $platform['platform']['name'];
                }

                $responseDetails = Http::get('https://api.rawg.io/api/games/' . $jogo['id'] . '?key=5ba552549cf8448b967a9d1788b45491');

                foreach ($responseDetails->json()['developers'] as $developer) {
                    $developers[] = $developer['name'];
                }


                $input = [
                    'name'              => $jogo['name'],
                    'description'       => $responseDetails->json()['description'],
                    'platforms'         => implode(', ', $platforms),
                    'developers'        => implode(', ', $developers),
                    'released'          => $jogo['released'],
                    'background_image'  => $jogo['background_image'],
                    'genres'            => implode(', ', $genres),
                    'tags'              => implode(', ', $tags),
                    'game_id'           => $jogo['id'],
                ];
                echo 'Criando registro do game ' . $jogo['name'] . ' de id: ' .$jogo['id']. '<br>';
                Game::create($input);
            }
        }
    }
}
