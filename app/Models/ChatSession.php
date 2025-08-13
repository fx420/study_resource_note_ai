<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatSession extends Model
{
    protected $fillable = ['user_id','title','education_level','course','topic','difficulty','mode'];
    
    public function messages()
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at');
    }

    public function showSession(ChatSession $session)
{
    $this->authorize('view', $session);
    // messages already eager‑loaded if you prefer:
    $session->load('messages');
    return view('chat.show', [
      'session'  => $session,
      'messages' => $session->messages,
    ]);
}
}
