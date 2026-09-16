<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Video API',
    description: 'API quản lý video lấy từ link YouTube (trích xuất ID, lấy metadata qua YouTube Data API v3).',
)]
#[OA\Server(
    url: 'http://localhost:8080/api',
    description: 'Local API server',
)]
#[OA\Tag(name: 'Videos', description: 'Quản lý video YouTube')]
class OpenApiInfo {}
