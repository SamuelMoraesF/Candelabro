<?php

namespace Candelabro\Services;

use Candelabro\Enums\CandleColor;
use Candelabro\ValueObject\Pray;
use GuzzleHttp\Client;
use RuntimeException;
use Symfony\Component\DomCrawler\Crawler;

class Church
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'headers' => [
                'User-Agent' => 'Candelabro (https://github.com/SamuelMoraesF/Candelabro)',
            ]
        ]);
    }

    public function prayFor(
        string $firstName,
        string $lastName,
        string $city,
        string $email,
        CandleColor $color,
        int $days,
    ): void {
        $this->client->post($color->getFormUrl(), [
            'form_params' => [
                'inputTempo' => $days,
                'inputNome' => $firstName,
                'inputSNome' => $lastName,
                'inputCidade' => $city,
                'inputEmail' => $email,
            ],
        ]);
    }

    public function prayList(
        string $email,
        CandleColor $color,
    ): array {
        $response = $this->client
            ->post($color->getListUrl(), [
                'form_params' => [
                    'filtrar' => $email,
                    'x' => '0',
                    'y' => '0',
                ],
            ])
            ->getBody()
            ->getContents();

        return $this->parseList($color, $response);
    }

    protected function parseList(CandleColor $color, string $response): array
    {
        $list = (new Crawler($response))
            ->filter('.section-vela-virtual .row .col-5bloco')
            ->each(fn (Crawler $node) => new Pray(
                name: $node->filter('p.color-green')->text(),
                date: $node->filter('p.color-red')->text(),
                city: $node->filter('p.color-blue')->text(),
                days: static::parseDays($node),
                color: $color,
            ));

        if (count($list) === 20) {
            return [];
        }

        return $list;
    }

    protected static function parseDays(Crawler $pray): int
    {
        $daysString = $pray->filter('p.pt-3.color-green')->text();

        if (preg_match('/^Vela de ([0-9]+) Dia\(s\)$/', $daysString, $matches) !== 1) {
            throw new RuntimeException('Could not parse days from prayer.');
        }

        return (int) $matches[1];
    }
}
