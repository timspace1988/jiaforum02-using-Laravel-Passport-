<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
		 \App\Models\Reply::class => \App\Policies\ReplyPolicy::class,
		 \App\Models\Topic::class => \App\Policies\TopicPolicy::class,
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //auto discover policy principle
        Gate::guessPolicyNamesUsing(function($modelClass){
            //e.g. App\Model\User' => 'App\Policies\UserPolicy'
            return 'App\Policies\\' . class_basename($modelClass) . 'Policy';
        });

        //Only 'Founder' can access Horizon page (Queue job monitor)
        \Horizon::auth(function($request){
            //Check if the current user is 'Founder'
            return \Auth::user()->hasRole('Founder');
        });

        //Passport provides some basic routes
        Passport::routes();
        //access token's expire time
        Passport::tokensExpireIn(now()->addDays(15));
        //refresh token's expire time
        Passport::refreshTokensExpireIn(now()->addDays(30));
    }
}
