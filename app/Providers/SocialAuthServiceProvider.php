<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SocialAuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        app()->afterResolving(AuthorizationServer::class,function(AuthorizationServer $server){
            $grant=$this->makeGrant();
            $server->enableGrantType($grant,Passport::tokensExpireIn());
        });
    }

    public function makeGrant(){
        $grant=new SocialGrant(
            $this->app->make(UserRepository::class),
            $this->app->make(RefreshTokenRepository::class)
        );
        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());
        return $grant;
    }
}
