<?php

namespace App\Http\Controllers;

use App\Jobs\SendMessage;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MsgController extends Controller
{
    public function index() {
        $user = User::where('id', auth()->id())->select([
            'id', 'name', 'email',
        ])->first();

        $messages = Message::with('user')->get()->append('time');

        return Inertia::render('Message/Index', [
            'user' => $user,
            'messages' => $messages,
        ]);
    }

    public function message(Request $request) {
        $message = Message::create([
            'user_id' => auth()->id(),
            'text' => $request->get('text'),
        ]);

        SendMessage::dispatch($message);
    }
}
