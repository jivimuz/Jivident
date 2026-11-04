<?php

return [
    'type_local_server' => (int) env('TYPE_LOCAL_SERVER', 0),
    'auth_server' => env('AUTH_SERVER', 'https://management.antariks.id/api/license/validate'),
];
