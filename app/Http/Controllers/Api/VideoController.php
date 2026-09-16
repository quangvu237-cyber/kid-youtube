<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVideoRequest;
use App\Http\Requests\UpdateVideoRequest;
use App\Services\VideoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use RuntimeException;

class VideoController extends Controller
{
    public function __construct(private readonly VideoService $videoService) {}

    #[OA\Get(
        path: '/videos',
        summary: 'Danh sách video phân trang',
        tags: ['Videos'],
        parameters: [
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 15, minimum: 1, maximum: 100)),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', minimum: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Thành công',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Video')),
                        new OA\Property(
                            property: 'meta',
                            properties: [
                                new OA\Property(property: 'current_page', type: 'integer'),
                                new OA\Property(property: 'last_page', type: 'integer'),
                                new OA\Property(property: 'per_page', type: 'integer'),
                                new OA\Property(property: 'total', type: 'integer'),
                                new OA\Property(property: 'from', type: 'integer', nullable: true),
                                new OA\Property(property: 'to', type: 'integer', nullable: true),
                            ],
                            type: 'object',
                        ),
                    ],
                ),
            ),
        ],
    )]
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);

        return response()->json(
            $this->videoService->list(max(1, min($perPage, 100)))
        );
    }

    #[OA\Post(
        path: '/videos',
        summary: 'Tạo video mới từ link YouTube (tự lấy metadata + statistics + comments)',
        tags: ['Videos'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['youtube_url'],
                properties: [
                    new OA\Property(property: 'youtube_url', type: 'string', format: 'uri', example: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'),
                    new OA\Property(property: 'title', type: 'string', nullable: true),
                ],
            ),
        ),
        responses: [
            new OA\Response(response: 201, description: 'Tạo thành công', content: new OA\JsonContent(ref: '#/components/schemas/Video')),
            new OA\Response(response: 422, description: 'Dữ liệu không hợp lệ hoặc không trích được YouTube ID'),
        ],
    )]
    public function store(StoreVideoRequest $request): JsonResponse
    {
        try {
            $payload = $this->videoService->create($request->validated());

            return response()->json($payload, 201);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    #[OA\Get(
        path: '/videos/{id}',
        summary: 'Chi tiết một video (đầy đủ metadata, statistics, embed_url)',
        tags: ['Videos'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', minimum: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Thành công', content: new OA\JsonContent(ref: '#/components/schemas/Video')),
            new OA\Response(response: 404, description: 'Không tìm thấy video'),
        ],
    )]
    public function show(int $id): JsonResponse
    {
        return response()->json($this->videoService->find($id));
    }

    #[OA\Get(
        path: '/videos/{id}/comments',
        summary: 'Danh sách bình luận (top-level) kèm replies của video',
        tags: ['Videos'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', minimum: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 20, minimum: 1, maximum: 100)),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', minimum: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Thành công',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/VideoComment')),
                        new OA\Property(
                            property: 'meta',
                            properties: [
                                new OA\Property(property: 'current_page', type: 'integer'),
                                new OA\Property(property: 'last_page', type: 'integer'),
                                new OA\Property(property: 'per_page', type: 'integer'),
                                new OA\Property(property: 'total', type: 'integer'),
                            ],
                            type: 'object',
                        ),
                    ],
                ),
            ),
            new OA\Response(response: 404, description: 'Không tìm thấy video'),
        ],
    )]
    public function comments(Request $request, int $id): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 20);
        $threads = $this->videoService->getCommentThreads($id, max(1, min($perPage, 100)));

        $data = collect($threads->items())
            ->map(fn ($comment) => $this->videoService->formatCommentWithReplies($comment))
            ->values();

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $threads->currentPage(),
                'last_page' => $threads->lastPage(),
                'per_page' => $threads->perPage(),
                'total' => $threads->total(),
            ],
        ]);
    }

    #[OA\Post(
        path: '/videos/{id}/refresh',
        summary: 'Làm mới metadata, statistics và comments của video từ YouTube Data API',
        tags: ['Videos'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', minimum: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Đã làm mới', content: new OA\JsonContent(ref: '#/components/schemas/Video')),
            new OA\Response(response: 404, description: 'Không tìm thấy video'),
            new OA\Response(response: 422, description: 'Không thể làm mới'),
        ],
    )]
    public function refresh(int $id): JsonResponse
    {
        try {
            return response()->json($this->videoService->refreshVideo($id));
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    #[OA\Put(
        path: '/videos/{id}',
        summary: 'Cập nhật video',
        tags: ['Videos'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', minimum: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'youtube_url', type: 'string', format: 'uri', nullable: true),
                    new OA\Property(property: 'youtube_id', type: 'string', nullable: true),
                    new OA\Property(property: 'title', type: 'string', nullable: true),
                    new OA\Property(property: 'channel', type: 'string', nullable: true),
                    new OA\Property(property: 'thumbnail', type: 'string', format: 'uri', nullable: true),
                    new OA\Property(property: 'status', type: 'string', enum: ['pending', 'completed', 'failed']),
                ],
            ),
        ),
        responses: [
            new OA\Response(response: 200, description: 'Thành công', content: new OA\JsonContent(ref: '#/components/schemas/Video')),
            new OA\Response(response: 404, description: 'Không tìm thấy video'),
            new OA\Response(response: 422, description: 'Dữ liệu không hợp lệ'),
        ],
    )]
    public function update(UpdateVideoRequest $request, int $id): JsonResponse
    {
        return response()->json(
            $this->videoService->update($id, $request->validated())
        );
    }

    #[OA\Delete(
        path: '/videos/{id}',
        summary: 'Xoá video',
        tags: ['Videos'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', minimum: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Đã xoá',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Video đã được xoá.'),
                    ],
                ),
            ),
            new OA\Response(response: 404, description: 'Không tìm thấy video'),
        ],
    )]
    public function destroy(int $id): JsonResponse
    {
        $this->videoService->delete($id);

        return response()->json(['message' => 'Video đã được xoá.']);
    }
}
