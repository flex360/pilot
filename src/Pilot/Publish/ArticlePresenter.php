<?php

namespace Flex360\Pilot\Pilot\Publish;

use Illuminate\Support\Str;
use Flex360\Pilot\Pilot\Site;

class ArticlePresenter
{
    protected $entity;

    public $image = null;

    public function __construct(Article $article)
    {
        $this->entity = $article;
        $this->image = $this->image();
    }

    public function image($width = null)
    {
        if (!isset($this->entity->assets[0])) {
            return null;
        }

        $asset = $this->entity->assets[0];
        $image = $asset->path;

        if ($asset->type == 'gallery') {
            if (isset($this->entity->assets[0]->assets[0])) {
                $asset = $this->entity->assets[0]->assets[0];
            }

            $image = $asset->path;
        }

        if (!empty($width)) {
            if ($width == 300 && strtotime($asset->published_on) <= strtotime('11/1/2012')) {
                // this asset predates the 300 wide image
                $image = '/img.php?zc=1&w=300&h=300&src='.$image;
            } elseif ($width == 405 && strtotime($asset->published_on) <= strtotime('11/1/2012')) {
                // this asset predates the 405 wide image
                $image = '/img.php?w=405&src='.$image;
            } else {
                $image = preg_replace('/\.jpg$/', '_'.$width.'$0', $image);
                $image = preg_replace('/\.JPG$/', '_'.$width.'$0', $image);
                $image = preg_replace('/\.jpeg$/', '_'.$width.'$0', $image);
                $image = preg_replace('/\.png$/', '_'.$width.'$0', $image);
            }
        }

        return $image;
    }

    public function asset_type()
    {
        return isset($this->entity->assets[0]) ? $this->entity->assets[0]->type : null;
    }

    public function images()
    {
        if ($this->entity->asset_type() == 'gallery') {
            $images = $this->entity->assets[0]->assets;
        } else {
            $images = $this->entity->assets;
        }

        return $images;
    }

    public function set_images($images)
    {
        if ($this->entity->asset_type() == 'gallery') {
            @$this->entity->assets[0]->assets = $images;
        } else {
            @$this->entity->assets = $images;
        }
    }

    public function url()
    {
        return 'http://' . Site::getCurrent()->domain . '/post/' . $this->entity->id . '/' . $this->entity->slug;
    }

    public function link()
    {
        return '<a href="' . $this->entity->url() . '">' . $this->entity->title . '</a>';
    }

    public function published_on($format = null)
    {
        $format = empty($format) ? 'n/j/Y g:i a' : $format;

        return date($format, strtotime($this->entity->published_on));
    }

    public function has_tag($tag_id = null)
    {
        if (is_null($tag_id)) {
            return false;
        }

        foreach ($this->entity->tags as $tag) {
            if ($tag->id == $tag_id) {
                return true;
            }
        }


        return false;
    }

    public function author()
    {
        return $this->entity->author->id; //display_name;
    }

    public function author_link()
    {
        $template = '<a href="mailto:%s">%s</a>';

        if ($this->entity->author->id == 0) {
            return sprintf($template, $this->entity->author_other_email, $this->entity->author_other_name);
        } else {
            return sprintf($template, $this->entity->author->email, $this->entity->author->display_name);
        }
    }

    public function summary()
    {
        return empty($this->entity->summary) ? limit_chars($this->entity->content, 400) : $this->entity->summary;
    }

    public function content()
    {
        $content = $this->entity->content;

        $html_image = '<div class="post-image-wrap %s"><div class="post-image">
                <img src="%s" data-credit="">
                <div class="post-image-credit">%s</div>
                <div class="share-overlay" style="display: none;">
                    <a href="%s" class="popup" data-width="500" data-height="500"><i class="fa fa-facebook-square fa-2x" title="Share on Facebook"></i> Share It</a>
                    <a href="%s" class="popup" data-width="500" data-height="400"><i class="fa fa-twitter-square fa-2x" title="Share on Twitter"></i> Tweet It</a>
                    <a href="%s" class="popup" data-width="500" data-height="400"><i class="fa fa-pinterest-square fa-2x" title="Pin on Pinterest"></i> Pin It</a>
                </div>
            </div>
            <div class="caption">%s</div>
        </div>';

        if (stripos($content, '#asset') !== false) {
            // we have manual placements for images
            $matches = array();
            preg_match_all('/(#asset\w*)/', $content, $matches);


            foreach ($matches[0] as $index => $match) {
                $images = $this->entity->images();

                $caption_class = 'caption-hidden';

                if ($match == '#asset_cutline') {
                    $caption_class = 'caption-visible';
                }

                if ($match == '#asset_all' || $match == '#asset_all_cutline') {
                    $html_gallery = '<div class="post-gallery">%s</div>';
                    $html_images = '';

                    foreach ($this->entity->images() as $image) {
                        $caption_class = $match == '#asset_all' ? 'caption-hidden' : 'caption-visible';
                        $html_images .= sprintf(
                            $html_image,
                            $caption_class,
                            $image->path,
                            $image->credit,
                            null, //Social::facebook_auto($this->entity, $image->path),
                            null, //Social::twitter($this->entity->url(), $this->entity->title),
                            null, //Social::pinterest($this->entity->url(), $image->path, $this->entity->summary),
                            $image->caption
                        );
                    }
                    $html_gallery = sprintf($html_gallery, $html_images);

                    $content = str_replace($match, $html_gallery, $content);

                    $this->entity->set_images(array());
                } else {
                    $html = sprintf(
                        $html_image,
                        $caption_class,
                        $images[$index]->path,
                        $images[$index]->credit,
                        null, //Social::facebook_auto($this->entity, $images[$index]->path),
                        null, //Social::twitter($this->entity->url(), $this->entity->title),
                        null, //Social::pinterest($this->entity->url(), $images[$index]->path, $this->entity->summary),
                        $images[$index]->caption
                    );

                    unset($images[$index]);

                    $this->entity->set_images($images);

                    $content = preg_replace('/' . $match . '/', $html, $content, 1);
                }
            }
        }

        if (stripos($content, '<!-- profiles:') !== false) {
            $matches = array();
            $result = preg_match('/<!-- profiles:(.*)-->/Uis', $content, $matches);

            if ($result === 1) {
                // _d($matches);
                $key = $matches[1];
                $keys = explode(':', $key);

                $profiles = profiles_index($keys[0], $keys[1]);

                $content = preg_replace('/<!-- profiles:' . $key . '-->/Uis', $profiles, $content, 1);
            }
        }

        // insert a place for the modal ad to live
        $paragraphs = substr_count($content, '</p>');
        $mobile_ad = '<div id="phone-ad-0" class="phone-ad" data-index="0"></div>';

        if ($paragraphs > 4) {
            // insert after second paragraph
            $cursor = 0;
            for ($i = 0; $i < 4; $i++) {
                $cursor = strpos($content, '</p>', $cursor+1);
            }
            $content = substr_replace($content, $mobile_ad, $cursor+4, 0);
        } else {
            // add to the bottom
            $content .= $mobile_ad;
        }

        return $content;
    }

    public function tag_links($wrap = null)
    {
        $wrap_arr = array('','');
        if (!empty($wrap)) {
            $wrap_arr = explode('*', $wrap);
        }

        $links = array();
        foreach ($this->entity->tags as $tag) {
            if (in_array($tag->parent_id, array(30589, 1091, 1098, 50237, 50238, 50239))) {
                $links[] = $wrap_arr[0] . '<a href="/category/' . $tag->id . '/' . Str::slug($tag->tag) . '">' . $tag->tag . '</a>' . $wrap_arr[1];
            }
        }

        return $links;
    }
}
