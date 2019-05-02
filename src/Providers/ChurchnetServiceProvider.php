<?php

namespace Bishopm\Churchnet\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Console\Scheduling\Schedule;
use Swift_SmtpTransport;
use Swift_Mailer;
use Form;

class ChurchnetServiceProvider extends ServiceProvider
{
    protected $commands = [
        'Bishopm\Churchnet\Console\BirthdayEmail',
        'Bishopm\Churchnet\Console\PlannedGivingReportEmail',
        'Bishopm\Churchnet\Console\PreacherReminder',
    ];

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->booted(function () {
            $schedule = app(Schedule::class);
            $schedule->command('churchnet:givingemails')->dailyAt('07:45');
            $schedule->command('churchnet:birthdayemail')->weekly()->mondays()->at('8:00');
        });
        if (! $this->app->routesAreCached()) {
            require __DIR__.'/../Http/api.routes.php';
            require __DIR__.'/../Http/web.routes.php';
        }
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'churchnet');
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
        config(['googlmapper.key' => 'AIzaSyATsm2WL8gJbhRVGzYIymMbYa78XFvIEPc']);
        config(['googlmapper.marker' => false]);
        config(['jwt.ttl' => 525600]);
        config(['jwt.refresh_ttl' => 525600]);
        config(['auth.providers.users.model'=>'Bishopm\Churchnet\Models\User']);
        config(['jwt.user' => 'Bishopm\Churchnet\Models\User']);
        config(['services.facebook.client_id' => env('FB_CLIENT_ID')]);
        config(['services.facebook.client_secret' => env('FB_CLIENT_SECRET')]);
        config(['services.facebook.redirect' => env('FB_REDIRECT')]);
        config(['services.google.client_id' => env('G_CLIENT_ID')]);
        config(['services.google.client_secret' => env('G_CLIENT_SECRET')]);
        config(['services.google.redirect' => env('G_REDIRECT')]);
        config(['taggable.model'=>'Bishopm\Churchnet\Models\Tagg']);
        config(['queue.default'=>'database']);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
        AliasLoader::getInstance()->alias("Mapper", 'Cornford\Googlmapper\Facades\MapperFacade');
        AliasLoader::getInstance()->alias("JWTFactory", 'Tymon\JWTAuth\Facades\JWTFactory');
        AliasLoader::getInstance()->alias("JWTAuth", 'Tymon\JWTAuth\Facades\JWTAuth');
        AliasLoader::getInstance()->alias("Form", 'Collective\Html\FormFacade');
        AliasLoader::getInstance()->alias("HTML", 'Collective\Html\HtmlFacade');
        AliasLoader::getInstance()->alias("Feeds", 'willvincent\Feeds\Facades\FeedsFacade');
        AliasLoader::getInstance()->alias("Socialite", 'Laravel\Socialite\Facades\Socialite');
        $this->app['router']->aliasMiddleware('handlecors', 'Barryvdh\Cors\HandleCors');
        $this->app['router']->aliasMiddleware('jwt.auth', 'Tymon\JWTAuth\Middleware\GetUserFromToken');
        $this->app['router']->aliasMiddleware('ispermitted', 'Bishopm\Churchnet\Middleware\IsPermitted');
        $this->app['router']->aliasMiddleware('isspecial', 'Bishopm\Churchnet\Middleware\IsSpecial');
        $this->app['router']->aliasMiddleware('isnametags', 'Bishopm\Churchnet\Middleware\IsNametags');
        $this->app->bind('user.mailer', function ($app, $parameters) {
            $smtp_host = array_get($parameters, 'smtp_host');
            $smtp_port = array_get($parameters, 'smtp_port');
            $smtp_username = array_get($parameters, 'smtp_username');
            $smtp_password = array_get($parameters, 'smtp_password');
            $smtp_encryption = array_get($parameters, 'smtp_encryption');
            $from_email = array_get($parameters, 'from_email');
            $from_name = array_get($parameters, 'from_name');
            $from_email = $parameters['from_email'];
            $from_name = $parameters['from_name'];
            $transport = new Swift_SmtpTransport($smtp_host, $smtp_port);
            $transport->setUsername($smtp_username);
            $transport->setPassword($smtp_password);
            $transport->setEncryption($smtp_encryption);
            $swift_mailer = new Swift_Mailer($transport);
            $mailer = new Mailer($app->get('view'), $swift_mailer, $app->get('events'));
            $mailer->alwaysFrom($from_email, $from_name);
            $mailer->alwaysReplyTo($from_email, $from_name);
            return $mailer;
        });
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
            'Bishopm\Churchnet\Repositories\GroupsRepository',
            function () {
                $repository = new \Bishopm\Churchnet\Repositories\GroupsRepository(new \Bishopm\Churchnet\Models\Group());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Churchnet\Repositories\HouseholdsRepository',
            function () {
                $repository = new \Bishopm\Churchnet\Repositories\HouseholdsRepository(new \Bishopm\Churchnet\Models\Household());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Churchnet\Repositories\IndividualsRepository',
            function () {
                $repository = new \Bishopm\Churchnet\Repositories\IndividualsRepository(new \Bishopm\Churchnet\Models\Individual());
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
            'Bishopm\Churchnet\Repositories\PeopleRepository',
            function () {
                $repository = new \Bishopm\Churchnet\Repositories\PeopleRepository(new \Bishopm\Churchnet\Models\Person());
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
            'Bishopm\Churchnet\Repositories\StatisticsRepository',
            function () {
                $repository = new \Bishopm\Churchnet\Repositories\StatisticsRepository(new \Bishopm\Churchnet\Models\Statistic());
                return $repository;
            }
        );
        $this->app->bind(
            'Bishopm\Churchnet\Repositories\TagsRepository',
            function () {
                $repository = new \Bishopm\Churchnet\Repositories\TagsRepository(new \Cviebrock\EloquentTaggable\Models\Tag());
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
