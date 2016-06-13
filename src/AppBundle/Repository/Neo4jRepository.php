<?php

namespace AppBundle\Repository;

use GraphAware\Common\Result\Record;
use GraphAware\Neo4j\Client\Client;

class Neo4jRepository
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return Record[]
     */
    public function getAllByDate()
    {
        $query = <<<CYPHER
MATCH (t1:Team)-[g:FACED]->(t2:Team)
RETURN g, t1, t2
ORDER BY g.played_on
CYPHER;

        return $this->getResults($query);
    }

    /**
     * @return int
     */
    public function getWorstFifaRanking()
    {
        $query = <<<CYPHER
MATCH (t:Team) RETURN MAX(t.fifa_ranking) AS max_fifa_ranking
CYPHER;

        return $this->getOneOrNullResult($query)->get('max_fifa_ranking');
    }

    /**
     * @param int $playedOn
     * @param string $team1Slug
     * @param string $team2Slug
     *
     * @return Record|null
     */
    public function getGameFromDateAndTeamSlugs($playedOn, $team1Slug, $team2Slug)
    {
        $query = <<<CYPHER
MATCH (t1:Team {slug: {slug_team_1}})-[g:FACED {played_on: {game_played_on}}]->(t2:Team {slug: {slug_team_2}})
RETURN g, t1, t2
CYPHER;

        return $this->getOneOrNullResult($query, [
            'game_played_on' => (int) $playedOn,
            'slug_team_1' => $team1Slug,
            'slug_team_2' => $team2Slug,
        ]);
    }

    /**
     * @param string $teamSlug
     * @param int    $currentPlayedOn
     *
     * @return Record[]
     */
    public function getPreviousGamesFromTeamSlug($teamSlug, $currentPlayedOn)
    {
        $query = <<<CYPHER
MATCH (t1:Team)-[g:FACED]->(t2:Team)
WHERE g.played_on < {current_game_played_on}
AND (t1.slug = {team_slug} OR t2.slug = {team_slug})
RETURN g, t1, t2
ORDER BY g.played_on DESC
CYPHER;

        return $this->getResults($query, [
            'team_slug' => $teamSlug,
            'current_game_played_on' => (int) $currentPlayedOn,
        ]);
    }

    /**
     * @param string $query
     * @param array  $parameters
     *
     * @return Record|null
     */
    private function getOneOrNullResult($query, array $parameters = [])
    {
        return $this->client->run($query, $parameters)->firstRecord();
    }

    /**
     * @param string $query
     * @param array  $parameters
     *
     * @return Record[]
     *
     * @throws \GraphAware\Neo4j\Client\Exception\Neo4jException
     */
    private function getResults($query, array $parameters = [])
    {
        return $this->client->run($query, $parameters)->records();
    }
}