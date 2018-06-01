<?php

namespace Bishopm\Churchnet\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Form;

class ChurchnetServiceProvider extends ServiceProvider
{
    protected $commands = [];

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if (! $this->app->routesAreCached()) {
            require __DIR__.'/../Http/api.routes.php';
            require __DIR__.'/../Http/web.routes.php';
        }
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'churchnet');
        $this->publishes([
        __DIR__.'/../Resources/views/errors' => base_path('resources/views/errors'),
        ]);
        $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');
        $this->publishes([__DIR__.'/../Assets' => public_path('vendor/bishopm'),], 'public');
        Form::component('bsText', 'churchnet::components.text', ['name', 'label' => '', 'placeholder' => '', 'value' => null, 'attributes' => []]);
        Form::component('bsPassword', 'churchnet::components.password', ['name', 'label' => '', 'placeholder' => '', 'value' => null, 'attributes' => []]);
        Form::component('bsTextarea', 'churchnet::components.textarea', ['name', 'label' => '', 'placeholder' => '', 'value' => null, 'attributes' => []]);
        Form::component('bsThumbnail', 'churchnet::components.thumbnail', ['source', 'width' => '100', 'label' => '']);
        Form::component('bsImgpreview', 'churchnet::components.imgpreview', ['source', 'width' => '200', 'label' => '']);
        Form::component('bsHidden', 'churchnet::components.hidden', ['name', 'value' => null]);
        Form::component('bsSelect', 'churchnet::components.select', ['name', 'label' => '', 'options' => [], 'value' => null, 'attributes' => []]);
        Form::component('pgHeader', 'churchnet::components.pgHeader', ['pgtitle', 'prevtitle', 'prevroute']);
        Form::component('pgButtons', 'churchnet::components.pgButtons', ['actionLabel', 'cancelRoute']);
        Form::component('bsFile', 'churchnet::components.file', ['name', 'attributes' => []]);
        config(['googlmapper.key' => 'AIzaSyBQmfbfWGd1hxfR1sbnRXdCaQ5Mx5FjUhA']);
        config(['googlmapper.marker' => false]);
        config(['jwt.ttl' => 525600]);
        config(['jwt.refresh_ttl' => 525600]);
        config(['auth.providers.users.model'=>'Bishopm\Churchnet\Models\User']);
        config(['jwt.user' => 'Bishopm\Churchnet\Models\User']);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        AliasLoader::getInstance()->alias("Mapper", 'Cornford\Googlmapper\Facades\MapperFacade');
        AliasLoader::getInstance()->alias("JWTFactory", 'Tymon\JWTAuth\Facades\JWTFactory');
        AliasLoader::getInstance()->alias("JWTAuth", 'Tymon\JWTAuth\Facades\JWTAuth');
        AliasLoader::getInstance()->alias("Form", 'Collective\Html\FormFacade');
        AliasLoader::getInstance()->alias("HTML", 'Collective\Html\HtmlFacade');
        $this->app['router']->aliasMiddleware('handlecors', 'Barryvdh\Cors\HandleCors');
        $this->app['router']->aliasMiddleware('jwt.auth', 'Tymon\JWTAuth\Middleware\GetUserFromToken');
        $this->registerBindings();
    }


    private function registerBindings()
    {
        $this->app->bind(
            'Bishopm\Churchnet\Repositories\CircuitsRepository',
            function () {
                $repository = new \Bishopm\Churchnet\Repositories\CircuitsRepository(new \Bishopm\Churchnet\Models\Circuit());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Churchnet\Repositories\DistrictsRepository',
            function () {
                $repository = new \Bishopm\Churchnet\Repositories\DistrictsRepository(new \Bishopm\Churchnet\Models\District());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Churchnet\Repositories\LabelsRepository',
            function () {
                $repository = new \Bishopm\Churchnet\Repositories\LabelsRepository(new \Bishopm\Churchnet\Models\Label());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Churchnet\Repositories\MeetingsRepository',
            function () {
                $repository = new \Bishopm\Churchnet\Repositories\MeetingsRepository(new \Bishopm\Churchnet\Models\Meeting());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Churchnet\Repositories\PagesRepository',
            function () {
                $repository = new \Bishopm\Churchnet\Repositories\PagesRepository(new \Bishopm\Churchnet\Models\Page());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Churchnet\Repositories\PlansRepository',
            function () {
                $repository = new \Bishopm\Churchnet\Repositories\PlansRepository(new \Bishopm\Churchnet\Models\Plan());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Churchnet\Repositories\PreachersRepository',
            function () {
                $repository = new \Bishopm\Churchnet\Repositories\PreachersRepository(new \Bishopm\Churchnet\Models\Preacher());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Churchnet\Repositories\ReadingsRepository',
            function () {
                $repository = new \Bishopm\Churchnet\Repositories\ReadingsRepository(new \Bishopm\Churchnet\Models\Reading());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Churchnet\Repositories\ResourcesRepository',
            function () {
                $repository = new \Bishopm\Churchnet\Repositories\ResourcesRepository(new \Bishopm\Churchnet\Models\Resource());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Churchnet\Repositories\ServicesRepository',
            function () {
                $repository = new \Bishopm\Churchnet\Repositories\ServicesRepository(new \Bishopm\Churchnet\Models\Service());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Churchnet\Repositories\SettingsRepository',
            function () {
                $repository = new \Bishopm\Churchnet\Repositories\SettingsRepository(new \Bishopm\Churchnet\Models\Setting());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Churchnet\Repositories\SocietiesRepository',
            function () {
                $repository = new \Bishopm\Churchnet\Repositories\SocietiesRepository(new \Bishopm\Churchnet\Models\Society());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Churchnet\Repositories\WeekdaysRepository',
            function () {
                $repository = new \Bishopm\Churchnet\Repositories\WeekdaysRepository(new \Bishopm\Churchnet\Models\Weekday());
                return $repository;
            }
        );
    }
}
