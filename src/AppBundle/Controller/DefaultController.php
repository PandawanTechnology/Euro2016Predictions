<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        return $this->render(':Default:index.html.twig', [
            'games' => $this->get('app.repository.neo4j')->getAllByDate(),
        ]);
    }

    /**
     * @param int $playedOn
     * @param string $team1Slug
     * @param string $team2Slug
     *
     * @return Response
     */
    public function showAction($playedOn, $team1Slug, $team2Slug)
    {
        $neo4jRepository = $this->get('app.repository.neo4j');

        if (!$result = $neo4jRepository->getGameFromDateAndTeamSlugs($playedOn, $team1Slug, $team2Slug)) {
            throw $this->createNotFoundException();
        }

        $game = $result->get('g');
        $gameDate = $game->get('played_on');
        $previousGamesTeam1 = $neo4jRepository->getPreviousGamesFromTeamSlug($team1Slug, $gameDate);
        $previousGamesTeam2 = $neo4jRepository->getPreviousGamesFromTeamSlug($team2Slug, $gameDate);

        return $this->render(':Default:show.html.twig', [
            'game' => $game,
            'team1' => $result->get('t1'),
            'team2' => $result->get('t2'),
            'game_has_score' => $game->containsKey('score_1') && $game->containsKey('score_2'),
            'previous_games_team1' => $previousGamesTeam1,
            'previous_games_team2' => $previousGamesTeam2,
            'has_previous_games' => count($previousGamesTeam1) || count($previousGamesTeam2),
            'prediction' => $this->get('app.predictor.game')->predict($result, $previousGamesTeam1, $previousGamesTeam2),
        ]);
    }
}
