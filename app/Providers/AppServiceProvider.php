<?php

namespace App\Providers;

use App\CentralLogics\Helpers;
use App\Routing\UrlGenerator as AppUrlGenerator;
use App\Traits\AddonHelper;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\UrlGenerator as IlluminateUrlGenerator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use ReflectionObject;

class AppServiceProvider extends ServiceProvider
{
    use AddonHelper;
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->extend('url', function (IlluminateUrlGenerator $original) {
            if (! config('app.strip_public_prefix_from_asset_urls', true)) {
                return $original;
            }

            $reflection = new ReflectionObject($original);
            $routes = $reflection->getProperty('routes')->getValue($original);
            $request = $reflection->getProperty('request')->getValue($original);
            $assetRoot = $reflection->getProperty('assetRoot')->getValue($original);

            $url = new AppUrlGenerator($routes, $request, $assetRoot);
            $targetReflection = new ReflectionObject($url);

            foreach ($reflection->getProperties() as $property) {
                if ($property->isStatic()) {
                    continue;
                }
                $name = $property->getName();
                if (in_array($name, ['routes', 'request', 'assetRoot'], true)) {
                    continue;
                }
                if (! $targetReflection->hasProperty($name)) {
                    continue;
                }
                $property->setAccessible(true);
                $targetProp = $targetReflection->getProperty($name);
                $targetProp->setAccessible(true);
                $targetProp->setValue($url, $property->getValue($original));
            }

            return $url;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        try
        {
            Config::set('addon_admin_routes',$this->get_addon_admin_routes());
            Config::set('get_payment_publish_status',$this->get_payment_publish_status());
            Paginator::useBootstrap();
            foreach(Helpers::get_view_keys() as $key=>$value)
            {
                view()->share($key, $value);
            }
        }
        catch(\Exception $e)
        {

        }

    }
}
