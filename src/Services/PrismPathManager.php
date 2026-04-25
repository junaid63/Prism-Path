<?php

namespace PrismPath\Analytics\Services;

use Illuminate\Contracts\Foundation\Application;

class PrismPathManager
{
    public function __construct(private readonly Application $app)
    {
    }

    public function script(array $options = []): string
    {
        if (! config('ultraclarity.enabled')) {
            return '';
        }

        $config = array_replace_recursive(config('ultraclarity.snippet'), $options);
        $endpoint = url($config['endpoint']);
        $payload = e(json_encode([
            'siteId' => config('ultraclarity.site_id'),
            'endpoint' => $endpoint,
            'sampleRate' => $config['sample_rate'],
            'gdpr' => $config['gdpr'],
            'maskInputs' => $config['mask_inputs'],
        ], JSON_THROW_ON_ERROR));

        return '<script>window.PrismPathConfig = ' . $payload . ';</script><script src="' . e(route('ultraclarity.snippet')) . '" ' . ($config['async'] ? 'async ' : '') . ($config['defer'] ? 'defer' : '') . '></script>';
    }
}

