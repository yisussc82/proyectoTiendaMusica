<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

#[Fillable(['client_id', 'address_line_1', 'address_line_2', 'city', 'province', 'postal_code', 'is_default'])]
class ClientAddress extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function markAsDefault(): void
    {
        DB::transaction(function () {
            $this->client->addresses()->whereKeyNot($this->id)->update(['is_default' => false]);
            $this->update(['is_default' => true]);
        });
    }
}
