<?php

return [
    'cache' => [
        /*
         * By default all features are cached for 24 hours.
         * When feature flags are updated the cache is flushed automatically.
         */
        'expiration_time' => \DateInterval::createFromDateString('24 hours'),

        /*
         * The cache key used to store features.
         */
        'key' => 'evolabs.feature_flags.cache',
    ],
];
