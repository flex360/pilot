<?php

namespace Flex360\Pilot\Pilot\Publish;

class Article extends Publish
{
    public static $publish_type = 'articles';

    public function present()
    {
        return new ArticlePresenter($this);
    }

    public function __call($function, $args = array())
    {
        $presenter = new ArticlePresenter($this);
        if (method_exists($presenter, $function)) {
            return call_user_func_array(array($presenter, $function), $args);
        }
    }

    public function related($cnt = 5)
    {
        $tags = array();
        foreach ($this->tags as $tag) {
            if ($tag->parent_id == \Page::getRoot()->getSetting('publish_tag')) {
                $tags[] = $tag;
            }
        }

        if (empty($tags)) {
            return array();
        }
        
        // just use the first tag for now
        $tags = array($tags[0]->id);

        // determine page of related posts to grab
        $page_num = rand(1, 4);

        $posts = Article::find(array(
            'tags' => implode(',', $tags),
            'pageStart' => $page_num,
            'pageSize' => $cnt+1
        ));

        // check for this post
        foreach ($posts as $key => $post) {
            if ($post->id == $this->id) {
                unset($posts[$key]);
            }
        }
        $posts = array_slice($posts, 0, $cnt);

        return $posts;
    }

    public static function index($params = array())
    {
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $params['pageStart'] = $page;

        if (isset($params['pageSize'])) {
            $pageSize = $params['pageSize'];
        } else {
            $pageSize = 5;
            $params['pageSize'] = $pageSize;
        }

        $articles = Article::find($params);

        return \View::make('publish.articles.index', compact('articles', 'page', 'pageSize'));
    }
}
