<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Models\Hotel;
use App\Models\Message;
use App\Services\ChatRecipientService;
use App\Services\MessageNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function __construct(
        private readonly MessageNotificationService $messageNotificationService,
        private readonly ChatRecipientService $chatRecipientService
    ) {}

    public function chat(Hotel $hotel): View
    {
        $this->authorize('viewAny', Message::class);

        $receivers = $this->chatRecipientService->receiversForHotel($hotel, auth()->user());

        return view('messages.chat', [
            'hotel' => $hotel,
            'receivers' => $receivers,
            'defaultReceiverId' => $this->chatRecipientService->defaultReceiverId($receivers, auth()->user()),
        ]);
    }

    public function index(Request $request, Hotel $hotel): JsonResponse
    {
        $this->authorize('viewAny', Message::class);

        $query = Message::query()
            ->where('hotel_id', $hotel->id)
            ->forParticipant(auth()->id())
            ->with(['sender:id,name', 'receiver:id,name'])
            ->orderBy('created_at')
            ->orderBy('id');

        if ($request->filled('after_id')) {
            $query->where('id', '>', (int) $request->query('after_id'));
        }

        $messages = $query->get();

        Message::query()
            ->where('hotel_id', $hotel->id)
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'messages' => $messages->map(fn (Message $message) => $this->messagePayload($message)),
        ]);
    }

    public function store(StoreMessageRequest $request, Hotel $hotel): JsonResponse
    {
        $this->authorize('create', Message::class);

        $message = Message::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $request->validated('receiver_id'),
            'hotel_id' => $hotel->id,
            'content' => $request->validated('content'),
        ]);

        $message->load(['sender:id,name', 'receiver:id,name']);

        $this->messageNotificationService->notifyReceiver($message);

        return response()->json([
            'message' => $this->messagePayload($message),
        ], 201);
    }

    private function messagePayload(Message $message): array
    {
        return [
            'id' => $message->id,
            'content' => $message->content,
            'sender_id' => $message->sender_id,
            'receiver_id' => $message->receiver_id,
            'sender_name' => $message->sender->name,
            'receiver_name' => $message->receiver->name,
            'is_mine' => $message->sender_id === auth()->id(),
            'created_at' => $message->created_at->format('d.m.Y H:i'),
        ];
    }
}
