<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'tanggal',
    'sentimen_positif',
    'sentimen_negatif',
    'sentimen_netral',
    'jumlah_bot_terdeteksi',
    'trending_issues',
])]
class CognitiveMetric extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'sentimen_positif' => 'integer',
            'sentimen_negatif' => 'integer',
            'sentimen_netral' => 'integer',
            'jumlah_bot_terdeteksi' => 'integer',
            'trending_issues' => 'array',
        ];
    }
}
