<?php

declare(strict_types=1);

namespace Kopling\Contact;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kopling\Core\Database\Model;
use Kopling\Core\People\Person;

class Message extends Model
{
    use HasUuids;

    protected $table = 'contact_messages';

    protected $fillable = [
        'name',
        'email',
        'subject',
        'context',
        'body',
        'person_id',
        'handled_at',
    ];

    protected function casts(): array
    {
        return [
            'handled_at' => 'datetime',
        ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function subjectLabel(): string
    {
        return Config::subjectLabel($this->subject);
    }

    public function sender(): string
    {
        return $this->name ?: $this->email;
    }
}
