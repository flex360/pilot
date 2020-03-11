<?php

namespace Flex360\Pilot\Pilot;

use GuzzleHttp\Client;

class NewsFeed
{
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function sync($console = null)
    {
        $data = $this->getData();

        foreach ($data as $article) {
            $validator = \Validator::make($article, [
                'title' => 'required',
                'external_link' => 'required|unique:posts',
                'summary' => 'required',
                'published_on' => 'required',
            ]);

            if ($validator->fails()) {
                // do nothing
                if (! empty($console)) {
                    $console->info('Item not added: "' . $article['title'] . '"');
                }
            } else {
                $post = \Post::create($article);

                // add any tags
                $post->tags()->sync($this->getTagIds());
            }
        }
    }

    public function getData()
    {
        $data = [];

        $feed = simplexml_load_string($this->getXml());

        foreach ($feed->channel->item as $article) {
            $articleData = [
                'status' => 30
            ];

            foreach ($this->schema as $key => $value) {
                if (is_string($value)) {
                    $articleData[$key] = (string) $article->$value;
                } elseif (is_array($value)) {
                    $articleKey = $value[0];
                    $articleValue = $value[1];

                    $articleData[$key] = (string) $articleValue($article->$articleKey);
                }
            }

            $data[] = $articleData;
        }

        return $data;
    }

    public function getXml()
    {
        $client = new Client([
            'base_uri' => $this->getBaseUri(),
            'timeout'  => 2.0,
        ]);

        $response = $client->get($this->getUri());

        $data = $response->getBody()->getContents();

        return $data;
    }

    public function getBaseUri()
    {
        $parts = explode('/', $this->url);

        return implode('/', [$parts[0], $parts[1], $parts[2]]) . '/';
    }

    public function getUri()
    {
        return str_replace($this->getBaseUri(), '', $this->url);
    }

    public function getTagIds()
    {
        $tagIds = [];

        if (isset($this->tags)) {
            $tags = $this->tags;

            if (! is_array($tags)) {
                $tags = [$tags];
            }

            foreach ($tags as $tagName) {
                $tag = \Tag::where('name', $tagName)->first();

                if (empty($tag)) {
                    $tag = \Tag::create(['name' => $tagName]);
                }

                $tagIds[] = $tag->id;
            }
        }

        return $tagIds;
    }
}
