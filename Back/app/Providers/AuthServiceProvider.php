<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\HairSalon;
use App\Models\Hairstyle;
use App\Models\Hairdresser;
use App\Policies\HairSalonPolicy;
use App\Policies\HairstylePolicy;
use App\Policies\HairdresserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        HairSalon::class => HairSalonPolicy::class,
        Hairdresser::class => HairdresserPolicy::class,
        Hairstyle::class => HairstylePolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
