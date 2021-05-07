<?php

namespace Flex360\Pilot\Pilot;

class PageAuthenticator
{

    protected $page;

    public function __construct($page)
    {
        $this->page = $page;
    }

    public function valid()
    {
        if (empty($this->page->password)) {
            return true;
        }

        return request()->cookie('page_auth') == 'yes';
    }

    public function check($password = null)
    {
        if (empty($password)) {
            $password = request()->get('password');
        }

        return $password == $this->page->password;
    }

    public function getPage()
    {
        $page = $this->page;

        if (! $this->valid()) {
            $page->block_1 = '';

            $page->block_2 = '';

            $page->body = view('pilot::frontend.pages.login', compact('page'))->render();
        }

        return $page;
    }

    public function getResponse()
    {
        return redirect($this->page->url())->withCookie('page_auth', $this->check() ?
        'yes' : 'no', 60, $this->page->path);
    }
}
