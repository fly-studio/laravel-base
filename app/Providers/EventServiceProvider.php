<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Addons\Core\Events\EventDispatcher;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $namespace = 'App';
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            'SocialiteProviders\Weixin\WeixinExtendSocialite@handle',
            'SocialiteProviders\WeixinWeb\WeixinWebExtendSocialite@handle',
            'SocialiteProviders\Weibo\WeiboExtendSocialite@handle',
            'SocialiteProviders\QQ\QqExtendSocialite@handle',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->resolveEventer();
    }

    private function resolveEventer()
    {
        app(EventDispatcher::class)->group(['namespace' => $this->namespace], function($eventer){
            require base_path('routes/event.php');
        });
    }
}
