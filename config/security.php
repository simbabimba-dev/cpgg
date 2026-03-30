<?php

return [
    'api_scopes' => [
        /*
         * Temporary backward compatibility toggle.
         * If true, tokens without explicit scopes can still access API routes.
         * Set to false to fully enforce explicit token scopes.
         */
        'allow_legacy_unscoped_tokens' => env('SECURITY_ALLOW_LEGACY_UNSCOPED_TOKENS', true),
    ],
];

