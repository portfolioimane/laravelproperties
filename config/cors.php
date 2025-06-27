<?php
return [
'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],  // Allow all HTTP methods

    'allowed_origins' => ['http://localhost', 'http://localhost:3000'],  // Allow requests from localhost and localhost:3000 (Next.js)

    'allowed_origins_patterns' => [],  // Optionally specify patterns

    'allowed_headers' => ['*'],  // Allow all headers

    'exposed_headers' => [],  // Optionally expose specific headers

    'max_age' => 0,  // Set caching for preflight requests

    'supports_credentials' => true,  // Allow credentials (cookies, auth headers)
];




