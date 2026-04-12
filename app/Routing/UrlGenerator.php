<?php

namespace App\Routing;

use Illuminate\Routing\UrlGenerator as BaseUrlGenerator;
use Illuminate\Support\Str;

class UrlGenerator extends BaseUrlGenerator
{
    /**
     * {@inheritdoc}
     *
     * When document root is the Laravel "public" directory, Blade often uses paths like
     * "public/assets/...". Those resolve incorrectly as "/public/public/...". Strip the
     * leading "public/" segment when enabled via config.
     */
    public function asset($path, $secure = null)
    {
        if (config('app.strip_public_prefix_from_asset_urls', true) && is_string($path)) {
            if (Str::startsWith($path, 'public/')) {
                $path = substr($path, strlen('public/'));
            } elseif (Str::startsWith($path, '/public/')) {
                $path = ltrim(substr($path, strlen('/public/')), '/');
            }
        }

        return parent::asset($path, $secure);
    }
}
