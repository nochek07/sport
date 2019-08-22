<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\GameBuffer;
use App\Entity\Language;
use App\Entity\League;
use App\Entity\Source;
use App\Entity\Sport;
use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/v1/api/add", name="api_post_v1", methods={"POST"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function index(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $manager = $this->getDoctrine()->getManager();

        if (isset($data['events'])) {
            foreach ($data['events'] as $event) {
                $lang = $manager
                    ->getRepository(Language::class)
                    ->findOneBy(['name' => $event['lang']]);
                if (is_null($lang)) {
                    $lang = new Language();
                    $lang->setName($event['lang']);
                    $manager->persist($lang);
                }

                $sport = $manager
                    ->getRepository(Sport::class)
                    ->findOneBy(['name' => $event['sport']]);
                if (is_null($sport)) {
                    $sport = new Sport();
                    $sport->setName($event['sport']);
                    $manager->persist($sport);
                }

                $league = $manager
                    ->getRepository(League::class)
                    ->findOneBy([
                        'name' => $event['league'],
                        'sport' => $sport
                    ]);
                if (is_null($league)) {
                    $league = new League();
                    $league->setName($event['league']);
                    $league->setSport($sport);
                    $manager->persist($league);
                }

                $team1 = $manager
                    ->getRepository(Team::class)
                    ->findOneBy([
                        'name' => $event['team1'],
                        'sport' => $sport
                    ]);
                if (is_null($team1)) {
                    $team1 = new Team();
                    $team1->setName($event['team1']);
                    $team1->setSport($sport);
                    $manager->persist($team1);
                }

                $team2 = $manager
                    ->getRepository(Team::class)
                    ->findOneBy([
                        'name' => $event['team2'],
                        'sport' => $sport
                    ]);
                if (is_null($team2)) {
                    $team2 = new Team();
                    $team2->setName($event['team2']);
                    $team2->setSport($sport);
                    $manager->persist($team2);
                }

                $source = $manager
                    ->getRepository(Source::class)
                    ->findOneBy(['name' => $event['source']]);
                if (is_null($source)) {
                    $source = new Source();
                    $source->setName($event['source']);
                    $manager->persist($source);
                }

                $date = new \DateTime($event['date']);

                // Game_Buffer
                $gameBuffer = $manager
                    ->getRepository(GameBuffer::class)
                    ->findOneBy([
                        'language' => $lang,
                        'league' => $league,
                        'team1' => $team1,
                        'team2' => $team2,
                        'date' => $date,
                        'source' => $source
                    ]);
                if (is_null($gameBuffer)) {
                    $gameBuffer = new GameBuffer();
                    $gameBuffer->setLanguage($lang);
                    $gameBuffer->setLeague($league);
                    $gameBuffer->setTeam1($team1);
                    $gameBuffer->setTeam2($team2);
                    $gameBuffer->setDate($date);
                    $gameBuffer->setSource($source);
                    $manager->persist($gameBuffer);
                }
                $manager->flush();

                $diff = $sport->getDiff();
                $dateStart = clone $date;
                $dateEnd = clone $date;
                $dateStart->modify("- {$diff} hour");
                $dateEnd->modify("+ {$diff} hour");

                /**
                 * @var Game $findGame
                 */
                $findGame = $manager
                    ->getRepository(Game::class)
                    ->findByBuffer($gameBuffer, $dateStart, $dateEnd);
                
                if (is_null($findGame)) {
                    $game = $this->addGame($gameBuffer);
                    $manager->persist($game);
                    $gameBuffer->setGame($game);
                } else {
                    $difference = $date->diff($findGame->getDate());
                    if ($difference->invert == 1) {
                        $findGame->setDate($date);
                    }
                }
                $manager->flush();

                echo '<pre>';
                print_r($league->getName());
                echo '</pre>';
            }

//            $manager->flush();
        }

        return $this->render('base.html.twig');
    }

    public function addGame(GameBuffer $gameBuffer)
    {
        $game = new Game();
        $game->setLeague($gameBuffer->getLeague());
        $game->setLanguage($gameBuffer->getLanguage());
        $game->setTeam1($gameBuffer->getTeam1());
        $game->setTeam2($gameBuffer->getTeam2());
        $game->setDate($gameBuffer->getDate());
        return $game;
    }
}