<?php

namespace AppBundle\Twig\Extension;

use GraphAware\Bolt\Record\RecordView;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class GameExtension extends \Twig_Extension
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'app_game';
    }

    /**
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @inheritDoc
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('game_outcome', [$this, 'getGameOutcome']),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('game_path', [$this, 'generatePath']),
        ];
    }

    /**
     * @param RecordView $record
     *
     * @return string
     */
    public function getGameOutcome(RecordView $record)
    {
        $result = $record->get('t1')->get('name');
        $game = $record->get('g');
        $result .= ' ';

        if ($game->containsKey('score_1') && $game->containsKey('score_2')) {
            $result .= $game->get('score_1') . ' - ' . $game->get('score_2');
        } else {
            $result .= '-';
        }
        $result .= ' ';

        return $result . $record->get('t2')->get('name');
    }

    /**
     * @param RecordView $record
     * @param int        $referenceType
     *
     * @return string
     */
    public function generatePath(RecordView $record, $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->urlGenerator->generate('game_show', [
            'playedOn' => $record->get('g')->get('played_on'),
            'team1Slug' => $record->get('t1')->get('slug'),
            'team2Slug' => $record->get('t2')->get('slug'),
        ], $referenceType);
    }
}