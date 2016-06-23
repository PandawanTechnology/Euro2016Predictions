<?php

namespace AppBundle\DataFixtures\Neo4j;

use GraphAware\Neo4j\Client\Connection\Connection;
use PandawanTechnology\Neo4jDataFixtures\AbstractNeo4jFixture;
use PandawanTechnology\Neo4jDataFixtures\Neo4jDependentFixtureInterface;

class LoadGameData extends AbstractNeo4jFixture implements Neo4jDependentFixtureInterface
{
    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [
            __NAMESPACE__.'\LoadTeamData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function load(Connection $connection)
    {
        $session = $connection->getSession();

        foreach ($this->getGames() as $game) {
            $session->run(<<<CYPHER
MATCH (t1:Team {slug: {slug_team_1}}), (t2:Team {slug: {slug_team_2}}) 
CREATE UNIQUE (t1)-[:FACED {played_on: {played_on}, score_1: {score_1}, score_2: {score_2}}]->(t2)
CYPHER
, [
                'slug_team_1' => $game[0],
                'slug_team_2' => $game[1],
                'played_on' => (int) (new \DateTime($game[2]))->format('U'),
                'score_1' => isset($game[3]) ? $game[3] : null,
                'score_2' => isset($game[4]) ? $game[4] : null,
            ]);
        }
    }

    /**
     * @return array
     */
    private function getGames()
    {
        return [
            [LoadTeamData::TEAM_FRANCE, LoadTeamData::TEAM_ROUMANIE, '2016-06-10 21:00:00', 2, 1],
            [LoadTeamData::TEAM_ALBANIE, LoadTeamData::TEAM_SUISSE, '2016-06-11 15:00:00', 0, 1],
            [LoadTeamData::TEAM_PAYS_DE_GALLE, LoadTeamData::TEAM_SLOVAQUIE, '2016-06-11 18:00:00', 2, 1],
            [LoadTeamData::TEAM_ANGLETERRE, LoadTeamData::TEAM_RUSSIE, '2016-06-11 21:00:00', 1, 1],
            [LoadTeamData::TEAM_TURQUIE, LoadTeamData::TEAM_CROATIE, '2016-06-12 15:00:00', 0, 1],
            [LoadTeamData::TEAM_POLOGNE, LoadTeamData::TEAM_IRLANDE_DU_NORD, '2016-06-12 18:00:00', 1, 0],
            [LoadTeamData::TEAM_ALLEMAGNE, LoadTeamData::TEAM_UKRAINE, '2016-06-12 21:00:00', 2, 0],
            [LoadTeamData::TEAM_ESPAGNE, LoadTeamData::TEAM_REPUBLIQUE_TCHEQUE, '2016-06-13 15:00:00', 1, 0],
            [LoadTeamData::TEAM_REPUBLIQUE_D_IRLANDE, LoadTeamData::TEAM_SUEDE, '2016-06-13 18:00:00', 1, 1],
            [LoadTeamData::TEAM_BELGIQUE, LoadTeamData::TEAM_ITALIE, '2016-06-13 21:00:00', 0, 2],
            [LoadTeamData::TEAM_AUTRICHE, LoadTeamData::TEAM_HONGRIE, '2016-06-14 18:00:00', 0, 2],
            [LoadTeamData::TEAM_PORTUGAL, LoadTeamData::TEAM_ISLANDE, '2016-06-14 21:00:00', 1, 1],

            [LoadTeamData::TEAM_RUSSIE, LoadTeamData::TEAM_SLOVAQUIE, '2016-06-15 15:00:00', 1, 2],
            [LoadTeamData::TEAM_ROUMANIE, LoadTeamData::TEAM_SUISSE, '2016-06-15 18:00:00', 1, 1],
            [LoadTeamData::TEAM_FRANCE, LoadTeamData::TEAM_ALBANIE, '2016-06-15 21:00:00', 2, 0],
            [LoadTeamData::TEAM_ANGLETERRE, LoadTeamData::TEAM_PAYS_DE_GALLE, '2016-06-16 15:00:00', 2, 1],
            [LoadTeamData::TEAM_UKRAINE, LoadTeamData::TEAM_IRLANDE_DU_NORD, '2016-06-16 18:00:00', 0, 2],
            [LoadTeamData::TEAM_ALLEMAGNE, LoadTeamData::TEAM_POLOGNE, '2016-06-16 21:00:00', 0, 0],
            [LoadTeamData::TEAM_ITALIE, LoadTeamData::TEAM_SUEDE, '2016-06-17 15:00:00', 1, 0],
            [LoadTeamData::TEAM_REPUBLIQUE_TCHEQUE, LoadTeamData::TEAM_CROATIE, '2016-06-17 18:00:00', 2, 2],
            [LoadTeamData::TEAM_ESPAGNE, LoadTeamData::TEAM_TURQUIE, '2016-06-17 21:00:00', 3, 0],
            [LoadTeamData::TEAM_BELGIQUE, LoadTeamData::TEAM_REPUBLIQUE_D_IRLANDE, '2016-06-18 15:00:00', 3, 0],
            [LoadTeamData::TEAM_ISLANDE, LoadTeamData::TEAM_HONGRIE, '2016-06-18 18:00:00', 1, 1],
            [LoadTeamData::TEAM_PORTUGAL, LoadTeamData::TEAM_AUTRICHE, '2016-06-18 21:00:00', 0, 0],

            [LoadTeamData::TEAM_SUISSE, LoadTeamData::TEAM_FRANCE, '2016-06-19 21:00:00', 0, 0],
            [LoadTeamData::TEAM_ROUMANIE, LoadTeamData::TEAM_ALBANIE, '2016-06-19 21:00:00', 0, 1],
            [LoadTeamData::TEAM_RUSSIE, LoadTeamData::TEAM_PAYS_DE_GALLE, '2016-06-20 21:00:00', 0, 3],
            [LoadTeamData::TEAM_SLOVAQUIE, LoadTeamData::TEAM_ANGLETERRE, '2016-06-20 21:00:00', 0, 0],
            [LoadTeamData::TEAM_UKRAINE, LoadTeamData::TEAM_POLOGNE, '2016-06-21 18:00:00', 0, 1],
            [LoadTeamData::TEAM_IRLANDE_DU_NORD, LoadTeamData::TEAM_ALLEMAGNE, '2016-06-21 18:00:00', 0, 1],
            [LoadTeamData::TEAM_REPUBLIQUE_TCHEQUE, LoadTeamData::TEAM_TURQUIE, '2016-06-21 21:00:00', 0, 2],
            [LoadTeamData::TEAM_CROATIE, LoadTeamData::TEAM_ESPAGNE, '2016-06-21 21:00:00', 2, 1],
            [LoadTeamData::TEAM_ISLANDE, LoadTeamData::TEAM_AUTRICHE, '2016-06-22 18:00:00', 2, 1],
            [LoadTeamData::TEAM_HONGRIE, LoadTeamData::TEAM_PORTUGAL, '2016-06-22 18:00:00', 3, 3],
            [LoadTeamData::TEAM_ITALIE, LoadTeamData::TEAM_REPUBLIQUE_D_IRLANDE, '2016-06-22 21:00:00', 0, 1],
            [LoadTeamData::TEAM_SUEDE, LoadTeamData::TEAM_BELGIQUE, '2016-06-22 21:00:00', 0, 1],

            [LoadTeamData::TEAM_SUISSE, LoadTeamData::TEAM_POLOGNE, '2016-06-25 15:00:00'],
            [LoadTeamData::TEAM_PAYS_DE_GALLE, LoadTeamData::TEAM_IRLANDE_DU_NORD, '2016-06-25 18:00:00'],
            [LoadTeamData::TEAM_CROATIE, LoadTeamData::TEAM_PORTUGAL, '2016-06-25 21:00:00'],
            [LoadTeamData::TEAM_FRANCE, LoadTeamData::TEAM_REPUBLIQUE_D_IRLANDE, '2016-06-26 15:00:00'],
            [LoadTeamData::TEAM_ALLEMAGNE, LoadTeamData::TEAM_SLOVAQUIE, '2016-06-26 18:00:00'],
            [LoadTeamData::TEAM_HONGRIE, LoadTeamData::TEAM_BELGIQUE, '2016-06-26 21:00:00'],
            [LoadTeamData::TEAM_ITALIE, LoadTeamData::TEAM_ESPAGNE, '2016-06-27 18:00:00'],
            [LoadTeamData::TEAM_ANGLETERRE, LoadTeamData::TEAM_ISLANDE, '2016-06-27 21:00:00'],
        ];
    }
}
