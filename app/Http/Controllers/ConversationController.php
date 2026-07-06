<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $isAdmin = in_array($user->role, ['admin', 'superadmin']);

        $conversations = Conversation::with(['user', 'lastMessage'])
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id))
            ->where('type', 'admin')
            ->latest()
            ->paginate(20);

        return view('pages.messages', compact('conversations', 'isAdmin'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:200',
            'body'    => 'required|string|max:2000',
        ]);

        $user = auth()->user();

        $conversation = Conversation::create([
            'ticket_no' => Conversation::generateTicketNo(),
            'user_id'   => $user->id,
            'type'      => 'admin',
            'status'    => 'open',
            'subject'   => $request->subject,
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'sender_type'     => 'user',
            'body'            => $request->body,
            'is_read'         => false,
        ]);

        // Auto-redirect to the conversation thread directly
        return redirect()->route('messages.show', $conversation)
            ->with('success', "Ticket {$conversation->ticket_no} opened. You can now chat with the admin.");
    }

    public function storeQuick()
    {
        // Quick-open from chatbot "Talk to Admin"
        $user = auth()->user();
        $conversation = Conversation::create([
            'ticket_no' => Conversation::generateTicketNo(),
            'user_id'   => $user->id,
            'type'      => 'admin',
            'status'    => 'open',
            'subject'   => 'General inquiry',
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => null,
            'sender_type'     => 'bot',
            'body'            => "Hello! This ticket was opened from the chatbot. An administrator will respond shortly.",
            'is_read'         => true,
        ]);

        return redirect()->route('messages.show', $conversation);
    }

    public function show(Conversation $conversation)
    {
        $user    = auth()->user();
        $isAdmin = in_array($user->role, ['admin', 'superadmin']);

        // Access control
        if (!$isAdmin && $conversation->user_id !== $user->id) {
            abort(403);
        }

        // Mark messages as read
        if ($isAdmin) {
            $conversation->messages()->where('sender_type', 'user')->update(['is_read' => true]);
        } else {
            $conversation->messages()->where('sender_type', 'admin')->update(['is_read' => true]);
        }

        $messages = $conversation->messages()->with('sender')->oldest()->get();

        return view('pages.message_show', compact('conversation', 'messages', 'isAdmin'));
    }

    public function reply(Request $request, Conversation $conversation)
    {
        $request->validate(['body' => 'required|string|max:2000']);

        $user    = auth()->user();
        $isAdmin = in_array($user->role, ['admin', 'superadmin']);

        if (!$isAdmin && $conversation->user_id !== $user->id) abort(403);

        if ($conversation->status !== 'open') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Conversation is closed.'], 403);
            }
            return back()->with('error', 'This conversation is closed.');
        }

        $message = \App\Models\Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'sender_type'     => $isAdmin ? 'admin' : 'user',
            'body'            => $request->body,
            'is_read'         => false,
        ]);

        $message->load('sender');

        if ($request->expectsJson()) {
            return response()->json([
                'id'          => $message->id,
                'body'        => $message->body,
                'sender_type' => $message->sender_type,
                'sender_name' => $message->sender->name ?? 'You',
                'time'        => $message->created_at->format('M d, Y h:i A'),
            ]);
        }

        return back();
    }

    public function close(Conversation $conversation)
    {
        $user    = auth()->user();
        $isAdmin = in_array($user->role, ['admin', 'superadmin']);
        if (!$isAdmin && $conversation->user_id !== $user->id) abort(403);

        $conversation->update(['status' => 'resolved']);
        return back()->with('success', 'Conversation marked as resolved.');
    }

    public function poll(Conversation $conversation, Request $request)
    {
        $user    = auth()->user();
        $isAdmin = in_array($user->role, ['admin', 'superadmin']);

        if (!$isAdmin && $conversation->user_id !== $user->id) abort(403);

        $sinceId  = $request->get('since_id', 0);
        $messages = $conversation->messages()
            ->with('sender')
            ->where('id', '>', $sinceId)
            ->oldest()
            ->get()
            ->map(fn($m) => [
                'id'          => $m->id,
                'body'        => $m->body,
                'sender_type' => $m->sender_type,
                'sender_name' => $m->sender->name ?? ($m->sender_type === 'bot' ? 'UCC-CS Bot' : 'User'),
                'time'        => $m->created_at->format('M d, Y h:i A'),
                'is_own'      => $m->sender_id === $user->id,
            ]);

        // Mark incoming messages as read
        $incomingType = $isAdmin ? 'user' : 'admin';
        $conversation->messages()
            ->where('sender_type', $incomingType)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'messages' => $messages,
            'last_id'  => $messages->last()['id'] ?? $sinceId,
        ]);
    }
}