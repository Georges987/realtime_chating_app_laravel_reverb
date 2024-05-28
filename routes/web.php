<?php

use App\Http\Controllers\MsgController;
use App\Jobs\SendMessage;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/messages', function () {
        $user = User::where('id', auth()->id())->select([
            'id', 'name', 'email',
        ])->first();

        $messages = Message::with('user')->get()->append('time');

        return Inertia::render('Message/Index', [
            'user' => $user,
            'messages' => $messages,
        ]);
    })->name('messages');

    Route::post('message', function () {
        $message = Message::create([
            'user_id' => auth()->id(),
            'text' => request()->text,
        ]);

        SendMessage::dispatch($message);
    })->name('message');

    Route::get('launcher', function () {
        Artisan::call('queue:work --queue=high,default --stop-when-empty');
        Artisan::call('reverb:start');

        return Inertia::render('Message/Index');
    })->name('launch');

    Route::get('stop', function () {
        Artisan::call('queue:clear');
        Artisan::call('reverb:stop');

        return Inertia::render('Dashboard',
            ['message' => 'Reverb stopped']
        );
    })->name('stop');
});
