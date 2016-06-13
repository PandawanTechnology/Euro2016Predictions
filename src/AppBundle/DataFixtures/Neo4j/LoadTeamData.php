<?php

namespace AppBundle\DataFixtures\Neo4j;

use GraphAware\Neo4j\Client\Connection\Connection;
use PandawanTechnology\Neo4jDataFixtures\AbstractNeo4jFixture;

class LoadTeamData extends AbstractNeo4jFixture
{
    const TEAM_ALBANIE = 'albanie';
    const TEAM_ALLEMAGNE = 'allemagne';
    const TEAM_ANGLETERRE = 'angleterre';
    const TEAM_AUTRICHE = 'autriche';
    const TEAM_BELGIQUE = 'belgique';
    const TEAM_CROATIE = 'croatie';
    const TEAM_ESPAGNE = 'espagne';
    const TEAM_FRANCE = 'france';
    const TEAM_HONGRIE = 'hongrie';
    const TEAM_IRLANDE_DU_NORD = 'irlande-du-nord';
    const TEAM_ISLANDE = 'islande';
    const TEAM_ITALIE = 'italie';
    const TEAM_PAYS_DE_GALLE = 'pays-de-galles';
    const TEAM_POLOGNE = 'pologne';
    const TEAM_PORTUGAL = 'portugal';
    const TEAM_REPUBLIQUE_D_IRLANDE = 'republique-irlande';
    const TEAM_REPUBLIQUE_TCHEQUE = 'republique-tcheque';
    const TEAM_ROUMANIE = 'roumanie';
    const TEAM_RUSSIE = 'russie';
    const TEAM_SLOVAQUIE = 'slovaquie';
    const TEAM_SUEDE = 'suede';
    const TEAM_SUISSE = 'suisse';
    const TEAM_TURQUIE = 'turquie';
    const TEAM_UKRAINE = 'ukraine';

    /**
     * @inheritdoc
     */
    public function load(Connection $connection)
    {
        $session = $connection->getSession();
        $keys = ['name', 'slug', 'fifa_ranking'];

        foreach ($this->getTeams() as $team) {
            $session->run('CREATE (t:Team) SET t += {infos} RETURN ID(t)', [
                'infos' => array_combine($keys, $team),
            ]);
        }
    }

    /**
     * @return array
     */
    private function getTeams()
    {
        return [
            ['France', static::TEAM_FRANCE, 17],
            ['Roumanie', static::TEAM_ROUMANIE, 22],
            ['Albanie', static::TEAM_ALBANIE, 42],
            ['Suisse', static::TEAM_SUISSE, 15],

            ['Angleterre', static::TEAM_ANGLETERRE, 11],
            ['Russie', static::TEAM_RUSSIE, 29],
            ['Pays de Galles', static::TEAM_PAYS_DE_GALLE, 26],
            ['Slovaquie', static::TEAM_SLOVAQUIE, 24],

            ['Allemagne', static::TEAM_ALLEMAGNE, 4],
            ['Ukraine', static::TEAM_UKRAINE, 19],
            ['Pologne', static::TEAM_POLOGNE, 27],
            ['Irlande du Nord', static::TEAM_IRLANDE_DU_NORD, 25],

            ['Espagne', static::TEAM_ESPAGNE, 6],
            ['République Tchèque', static::TEAM_REPUBLIQUE_TCHEQUE, 30],
            ['Turquie', static::TEAM_TURQUIE, 18],
            ['Croatie', static::TEAM_CROATIE, 27],

            ['Belgique', static::TEAM_BELGIQUE, 2],
            ['Italie', static::TEAM_ITALIE, 12],
            ['République d\'Irlande', static::TEAM_REPUBLIQUE_D_IRLANDE, 33],
            ['Suède', static::TEAM_SUEDE, 35],

            ['Portugal', static::TEAM_PORTUGAL, 8],
            ['Islande', static::TEAM_ISLANDE, 34],
            ['Autriche', static::TEAM_AUTRICHE, 10],
            ['Hongrie', static::TEAM_HONGRIE, 20],
        ];
    }
}
