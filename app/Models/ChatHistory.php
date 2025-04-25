<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatHistory extends Model
{
    protected $fillable = [
        'session_id', 
        'lead_message', 
        'assistant_response', 
        'meta_data'
    ];

    // app/Models/ChatHistory.php
protected $casts = [
    'meta_data' => 'array'
];
public function setMetaDataAttribute($value)
{
    $this->attributes['meta_data'] = json_encode([
        'nom' => $value['nom'] ?? '',
        'email' => $value['email'] ?? '',
        'numero' => $value['numero'] ?? '',
       'recap_discussion' => $value['recap_discussion'] ?? ''
    ]);
}
}