<?php

namespace AppBundle\Predictor;

use AppBundle\Repository\Neo4jRepository;
use GraphAware\Bolt\Record\RecordView;
use GraphAware\Bolt\Result\Type\Node;
use GraphAware\Bolt\Result\Type\Relationship;
use GraphAware\Common\Result\Record;

class GamePredictor
{
    /**
     * @var int
     */
    protected $highestFifaRanking;

    /**
     * @param Neo4jRepository $repository
     */
    public function __construct(Neo4jRepository $repository)
    {
        $this->highestFifaRanking = $repository->getWorstFifaRanking();
    }

    /**
     * @param Record       $resultSet
     * @param RecordView[] $previousGamesTeam1
     * @param RecordView[] $previousGamesTeam2
     *
     * @return array|null
     */
    public function predict(Record $resultSet, array $previousGamesTeam1, array $previousGamesTeam2)
    {
        $team1 = $resultSet->get('t1');
        $team2 = $resultSet->get('t2');

        $predictionTeam1 = $this->getPercentForTeam($team1, $previousGamesTeam1);
        $predictionTeam2 = $this->getPercentForTeam($team2, $previousGamesTeam2);

        $teamWeightDifference = $this->getTeamWeightDifference($team1, $resultSet);
        $predictionTeam1 += (abs((1/$this->highestFifaRanking) * $teamWeightDifference)) / 2;
        $predictionTeam2 += (abs((1/$this->highestFifaRanking) * $teamWeightDifference)) / 2;

        $nbGamesTeam1 = $this->countGamesWithScore($previousGamesTeam1);
        $nbGamesTeam2 = $this->countGamesWithScore($previousGamesTeam2);

        if (!$nbGamesTeam1 || !$nbGamesTeam2) {
            return $this->generatePredictionsFromRankings($team1, $team2);
        }

        $totalPrediction1 = ($predictionTeam1 * $nbGamesTeam1)/$nbGamesTeam1;
        $totalPrediction2 = ($predictionTeam2 * $nbGamesTeam2)/$nbGamesTeam2;
        $totalNbGames = $nbGamesTeam1 + $nbGamesTeam2;

        $team1Prediction = $totalPrediction1/$totalNbGames;
        $team2Prediction = $totalPrediction2/$totalNbGames;

        return [
            $team1Prediction,
            1 - $team1Prediction - $team2Prediction,
            $team2Prediction,
        ];
    }

    /**
     * @param Node  $team
     * @param array $previousGameResultSets
     *
     * @return float|null
     */
    private function getPercentForTeam($team, array $previousGameResultSets)
    {
        if (!count($previousGameResultSets)) {
            return;
        }

        $prediction = 0.5;

        foreach ($previousGameResultSets as $previousGameResultSet) {
            /** @var Relationship $previousGame */
            $previousGame = $previousGameResultSet->get('g');

            if (!$previousGame->containsKey('score_1') || !$previousGame->containsKey('score_2')) {
                continue;
            }

            $prediction = $this->getPredictionForGame($team, $previousGameResultSet, $prediction);
        }

        return $prediction;
    }

    /**
     * @param Node       $team
     * @param RecordView $resultSet
     *
     * @return float
     */
    private function getTeamOutcome(Node $team, RecordView $resultSet)
    {
        $teamGuest = $resultSet->get('t2');
        $game = $resultSet->get('g');

        $currentTeamScore = $game->get('score_1');
        $opponentTeamScore = $game->get('score_2');

        if ($teamGuest == $team) {
            $currentTeamScore = $game->get('score_2');
            $opponentTeamScore = $game->get('score_1');
        }

        if ($currentTeamScore > $opponentTeamScore) {
            return 1.0;
        }

        if ($currentTeamScore < $opponentTeamScore) {
            return -1.0;
        }

        return 0.0;
    }

    /**
     * @param Node       $team
     * @param Record     $resultSet
     *
     * @return int
     */
    private function getOpponentWeight(Node $team, Record $resultSet)
    {
        return $this->highestFifaRanking + 1 - ($resultSet->get('t1') == $team ? $resultSet->get('t2')->get('fifa_ranking') : $resultSet->get('t1')->get('fifa_ranking'));
    }

    /**
     * @param Node       $team
     * @param RecordView $previousGameResultSet
     * @param float      $prediction
     *
     * @return float
     */
    private function getPredictionForGame(Node $team, RecordView $previousGameResultSet, $prediction)
    {
        $outcomeWeight = $this->getTeamOutcome($team, $previousGameResultSet);
        $teamWeightDifference = $this->getTeamWeightDifference($team, $previousGameResultSet);

        return $prediction + ($outcomeWeight - abs((1/$this->highestFifaRanking) * $teamWeightDifference)) / 2;
    }

    /**
     * @param Node       $team
     * @param Record     $previousGameResultSet
     *
     * @return int
     */
    private function getTeamWeightDifference(Node $team, Record $previousGameResultSet)
    {
        $opponentWeight = $this->getOpponentWeight($team, $previousGameResultSet);
        $teamWeight = $this->highestFifaRanking + 1 - $team->get('fifa_ranking');

        return $opponentWeight - $teamWeight;
    }

    /**
     * @param RecordView[] $recordViews
     *
     * @return int
     */
    private function countGamesWithScore(array $recordViews)
    {
        if (!count($recordViews)) {
            return 0;
        }

        $filtered = array_filter($recordViews, function($recordView) {
            /** @var Record $game */
            $game = $recordView->get('g');

            return $game->hasValue('score_1') && $game->hasValue('score_2');
        });

        return count($filtered);
    }

    /**
     * @param Node $team1
     * @param Node $team2
     *
     * @return array
     */
    private function generatePredictionsFromRankings(Node $team1, Node $team2)
    {
        $team1RankingWeight = $this->highestFifaRanking - $team1->get('fifa_ranking');
        $team2RankingWeight = $this->highestFifaRanking - $team2->get('fifa_ranking');

        $total = $team1RankingWeight + $team2RankingWeight;

        return [
            $team1RankingWeight / $total,
            0,
            $team2RankingWeight / $total,
        ];
    }
}