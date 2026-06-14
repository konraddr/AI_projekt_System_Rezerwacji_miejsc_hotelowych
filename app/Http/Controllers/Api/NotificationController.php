<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = min(max($request->integer('per_page', 20), 1), 50);

        $notifications = $this->notificationService->paginatedForUser(
            $request->user(),
            $perPage
        );

        return NotificationResource::collection($notifications)->response();
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json([
            'count' => $this->notificationService->unreadCountForUser($request->user()),
        ]);
    }

    public function markAsRead(Request $request, string $notification): JsonResponse
    {
        $item = $this->notificationService->markAsRead($request->user(), $notification);

        return (new NotificationResource($item))
            ->response()
            ->setStatusCode(200);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $markedCount = $this->notificationService->markAllAsRead($request->user());

        return response()->json([
            'message' => 'Wszystkie powiadomienia oznaczono jako przeczytane.',
            'marked_count' => $markedCount,
        ]);
    }
}
