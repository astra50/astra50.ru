<?php

declare(strict_types=1);

namespace App\VK\Sections;

use App\VK\Model\Album;
use App\VK\Model\Photo;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Photos
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param       $ownerId
     * @param       $albumIds
     * @param array $options
     *
     * @return \Generator|Album[]
     */
    public function getAlbums(int $owner, $albums, array $options = []): \Generator
    {
        $response = $this->client->get('/method/photos.getAlbums', [
            RequestOptions::QUERY => array_replace($options, [
                'owner_id' => $owner,
                'album_ids' => implode(',', (array) $albums),
            ]),
        ]);

        foreach ((array) $response as $album) {
            yield new Album($album);
        }
    }

    /**
     * @param int   $owner
     * @param int   $album
     * @param array $options
     *
     * @return \Generator|Photo[]
     */
    public function get(string $owner, string $album, array $options = []): \Generator
    {
        $response = $this->client->get('/method/photos.get', [
            RequestOptions::QUERY => array_replace($options, [
                'owner_id' => $owner,
                'album_id' => $album,
            ]),
        ]);

        foreach ((array) $response as $item) {
            yield new Photo($item);
        }
    }
}
