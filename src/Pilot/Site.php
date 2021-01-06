<?php

namespace Flex360\Pilot\Pilot;

use Config;
use Illuminate\Support\Str;
use Spatie\Image\Manipulations;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media as SpatieMedia;
use Flex360\Pilot\Pilot\Traits\HasEmptyStringAttributes;

class Site extends Model implements HasMedia
{
    use HasMediaTrait,
        SoftDeletes,
        HasEmptyStringAttributes;

    protected $table = 'sites';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $emptyStrings = ['name', 'domain'];

    public static $backend = false;

    public $cssConfig = [
        'site-color-1' => [
            'type' => 'color',
            'label' => 'Site Color 1',
        ],

        'site-color-2' => [
            'type' => 'color',
            'label' => 'Site Color 2',
        ],

        'site-color-3' => [
            'type' => 'color',
            'label' => 'Site Color 3',
        ],

        'site-color-4' => [
            'type' => 'color',
            'label' => 'Site Color 4',
        ],

        'site-color-5' => [
            'type' => 'color',
            'label' => 'Site Color 5',
        ],

        'site-color-6' => [
            'type' => 'color',
            'label' => 'Site Color 6',
        ],

        'site-color-7' => [
            'type' => 'color',
            'label' => 'Site Color 7',
        ],

        'site-color-8' => [
            'type' => 'color',
            'label' => 'Site Color 8',
        ],

        'header-bg-image' => [
            'type' => 'image',
            'label' => 'Header Background Image',
            'help' => 'Dimensions: 2000 x 300 pixels',
            'width' => 2000,
            'height' => 300,
        ],

        'font-1' => [
            'type' => 'font',
            'label' => 'Font 1',
            'options' => [
                '' => '',
                'Droid Serif' => 'Droid Serif',
                'Roboto Condensed' => 'Roboto Condensed',
                'Lora' => 'Lora',
                'Oswald' => 'Oswald',
                'Raleway' => 'Raleway',
                'Titillium Web' => 'Titillium Web',
                'Libre Baskerville' => 'Libre Baskerville',
                'Open Sans' => 'Open Sans',
            ],
        ],

        'font-2' => [
            'type' => 'font',
            'label' => 'Font 2',
            'options' => [
                '' => '',
                'Droid Serif' => 'Droid Serif',
                'Roboto Condensed' => 'Roboto Condensed',
                'Lora' => 'Lora',
                'Oswald' => 'Oswald',
                'Raleway' => 'Raleway',
                'Titillium Web' => 'Titillium Web',
                'Libre Baskerville' => 'Libre Baskerville',
                'Open Sans' => 'Open Sans',
            ],
        ],

        'font-3' => [
            'type' => 'font',
            'label' => 'Font 3',
            'options' => [
                '' => '',
                'Droid Serif' => 'Droid Serif',
                'Roboto Condensed' => 'Roboto Condensed',
                'Lora' => 'Lora',
                'Oswald' => 'Oswald',
                'Raleway' => 'Raleway',
                'Titillium Web' => 'Titillium Web',
                'Libre Baskerville' => 'Libre Baskerville',
                'Open Sans' => 'Open Sans',
            ],
        ],
    ];

    public static function init()
    {
        $sites = Cache::rememberForever('sites', function () {
            return self::all();
        });

        if ($sites->isEmpty()) {
            $fullDomain = \Request::server('HTTP_HOST');
            $domain = str_replace('www.', '', $fullDomain);

            self::create([
                'name' => $domain,
                'domain' => $domain
            ]);

            Cache::forget('sites');
        }
    }

    public static function boot()
    {
        parent::boot();

        self::saved(function ($site) {
            Cache::forget('sites');
            Cache::forget('custom_css_' . $site->id);
            Cache::forget('page-root');
        });

        self::deleted(function ($site) {
            Cache::forget('sites');
        });
    }

    /**
     * getCurrent
     *
     * @return \App\Pilot\Site
     */
    public static function getCurrent()
    {
        $site = Config::get('site');

        if (empty($site)) {
            $site = Site::firstOrNew();
        }

        return $site;
    }

    public static function setCurrent()
    {
        // determine the domain
        $domain = self::getDomain();

        // cache list of sites
        $sites = Cache::rememberForever('sites', function () {
            return self::all();
        });

        // set site to null for default
        $site = new Site;
        Config::set('site', $site);
        

        // look for this site
        foreach ($sites as $s) {
            if (in_array($domain, $s->getDomains()) !== false || in_array('!' . $domain, $s->getDomains()) !== false) {
                $site = $s;
                Config::set('site', $site);
            }
        }
        

        // if there is only one site, use it fool
        if ($sites->count() == 1) {
            $site = $sites->first();
            Config::set('site', $sites->first());
        }

        return $site;
    }

    public function getDomains()
    {
        return explode(',', str_replace(', ', ',', $this->domain));
    }

    public function hasDefaultDomain()
    {
        $domains = $this->getDomains();

        foreach ($domains as $domain) {
            if (substr($domain, 0, 1) == '!') {
                return true;
            }
        }

        return false;
    }

    public function getDefaultDomain()
    {
        foreach ($this->getDomains() as $domain) {
            if (substr($domain, 0, 1) == '!') {
                return str_replace('!', '', $domain);
            }
        }

        return self::getDomain();
    }

    public static function getDomain()
    {
        $fullDomain = self::getFullDomain();
        return str_replace('www.', '', $fullDomain);
    }

    public static function getFullDomain()
    {
        return \Request::server('HTTP_HOST');
    }

    public function getAttemptedProtocol()
    {
        return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    }

    public function getDefaultProtocol()
    {
        return $this->force_https == true ? 'https' : $this->getAttemptedProtocol();
    }

    public function hasIncorrectProtocol()
    {
        return $this->getAttemptedProtocol() != $this->getDefaultProtocol();
    }

    public static function setDomain()
    {
        $domain = self::getDomain();
        Config::set('site.domain', $domain);
    }

    public function needsRedirect()
    {
        // protect the admin so that bad redirects don't break it
        // if (request()->is('pilot') || request()->is('pilot/*')) {
        //     return false;
        // }

        if ($this->hasDefaultDomain() && self::getDomain() != $this->getDefaultDomain()) {
            return true;
        }

        if ($this->hasIncorrectProtocol()) {
            return true;
        }

        if ($this->force_www && substr(self::getFullDomain(), 0, 4) != 'www.') {
            return true;
        }

        return false;
    }

    public function getRedirect()
    {
        $protocol = $this->getDefaultProtocol();
        $subdomain = $this->force_www ? 'www.' : null;
        $domain = $this->getDefaultDomain();
        $path = trim(request()->path(), '/');

        return redirect($protocol . '://' . $subdomain . $domain . '/' . $path);
    }

    public function getCssClassName()
    {
        return Str::slug(str_replace(':8000', '', $this->domain));
    }

    public function getCssVariables()
    {
        return is_array($this->css) ? $this->css : [];
    }

    public function getCssProperty($property)
    {
        if (!empty($this->css)) {
            return isset($this->css[$property]) ? $this->css[$property] : null;
        } else {
            return null;
        }
    }

    /**
     * Decodes the JSON string stored in css property
     * @param string $value
     * @return array
     */
    public function getCssAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * Converts the css property to a JSON string when set
     * @param array $value
     */
    public function setCssAttribute($value)
    {
        $this->attributes['css'] = json_encode($value);
    }

    /**
     * Get css value from css store
     * @param string $key
     * @return mixed
     */
    public function getCssValue($key, $default = null)
    {
        $value = $this->css[$key];

        if (empty($value)) {
            return $default;
        } else {
            return $value;
        }
    }

    public function getCssConfigByType($type)
    {
        $typeArray = [];

        foreach ($this->cssConfig as $name => $options) {
            if ($options['type'] == $type) {
                $typeArray[$name] = $options;
            }
        }

        return $typeArray;
    }

    public static function getRequestTitle()
    {
        return self::getTitleFromRequest();
    }

    public static function getTitleFromRequest()
    {
        $part = UrlHelper::getPart(2);

        if ($part === false) {
            return 'Pages';
        }

        if ($part === 'post') {
            return 'News';
        }

        $class = self::getClassFromRequest();

        $lastPart = class_basename($class);

        $title = ucwords(str_replace('_', ' ', Str::snake($lastPart)));

        return Str::plural($title);
    }

    public static function getClassFromRequest()
    {
        $part = UrlHelper::getPart(2);

        $class = collect(get_declared_classes())->first(function ($class, $index) use ($part) {
            $parts = explode('\\', strtolower($class));

            return in_array($part, $parts);
        });

        return $class;
    }

    public static function requestOfClass($class)
    {
        $requestClass = self::getClassFromRequest();

        return $requestClass == $class;
    }

    public static function isBackend()
    {
        // return self::$backend;
        return request()->is('pilot') || request()->is('pilot/*');
    }

    public function initLearnPages()
    {
        if (self::isBackend()) {
            // $learnPage = Page::where('title', 'CMS Guides')->first();
            $learnPage = Cache::rememberForever('pilot-learn-root', function () {
                
                return Page::findByPath('/learn');
            });

            if (empty($learnPage)) {
                // we are clear to add the pages because they don't exist
                $root = Page::getRoot();

                $page = new Page;
                $page->title = 'Learn';
                $page->status = 'hidden';
                $page->layout = 'layouts.internal';
                $page->parent_id = $root->id;
                $page->type_id = 1;
                $page->position = 9999;
                $page->save();

                $page->title = 'CMS Guides';
                $page->save();

                $learnPage = $page;

                $subPageData = [
                    [
                        'view' => 'cms-intro',
                        'title' => 'CMS Introduction'
                    ],
                    [
                        'view' => 'wysiwyg',
                        'title' => 'WYSIWYG Editor',
                    ],
                    [
                        'view' => 'settings',
                        'title' => 'Page Settings',
                    ],
                    [
                        'view' => 'metadata',
                        'title' => 'Metadata',
                    ],
                    [
                        'view' => 'code',
                        'title' => 'Code',
                    ],
                    [
                        'view' => 'style',
                        'title' => 'Internal Page Layout',
                    ],
                    [
                        'view' => 'alert-module-test',
                        'title' => 'Alert Module Test',
                    ]
                ];

                foreach ($subPageData as $data) {
                    $page = new Page;
                    $page->title = $data['view'];
                    $page->status = 'hidden';
                    $page->layout = 'layouts.internal';
                    $page->parent_id = $learnPage->id;
                    $page->type_id = 1;
                    $page->body = (string) view('pilot::admin.learn.' . $data['view']);
                    $page->save();

                    $page->title = $data['title'];
                    if ($page->title == 'CMS Introduction') {
                        $page->addMedia(public_path('pilot-assets/img/FLEX360_learn.jpg'))->preservingOriginal()->toMediaCollection('featured_image');
                    }
                    if ($page->title == 'Internal Page Layout') {
                        $page->addMedia(public_path('pilot-assets/img/FLEX360_learn.jpg'))->preservingOriginal()->toMediaCollection('featured_image');
                    }
                    if ($page->title == 'Alert Module Test') {
                        $page->addMedia(public_path('pilot-assets/img/alertModuleTest.jpg'))->preservingOriginal()->toMediaCollection('featured_image');
                    }
                    $page->save();
                }
            }
        }
    }

    public static function needsAuthRedirect()
    {
        return config('auth.allow_frontend_login') === true &&
                config('auth.protect_entire_site') === true &&
                Auth::guest() &&
                request()->path() != 'login' &&
                request()->path() != 'register';
    }

    public function registerMediaConversions(SpatieMedia $media = null)
    {
        // let's always use standard names like thumb, xsmall, small, medium, large, xlarge

        $this->addMediaConversion('thumb')
             ->crop(Manipulations::CROP_TOP_RIGHT, 300, 300)
             ->nonQueued();

        $this->addMediaConversion('small')
             ->width(300)
             ->height(300)
             ->nonQueued();
    }
}
