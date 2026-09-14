<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\DocFeedbackFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $doc_page_id
 * @property int|null $user_id
 * @property bool $helpful
 * @property string|null $comment
 * @property string $visitor_hash
 */
#[Fillable(['doc_page_id', 'user_id', 'helpful', 'comment', 'visitor_hash'])]
final class DocFeedback extends Model
{
    /** @use HasFactory<DocFeedbackFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['helpful' => 'boolean'];
    }

    /** @return BelongsTo<DocPage, $this> */
    public function page(): BelongsTo
    {
        return $this->belongsTo(DocPage::class, 'doc_page_id');
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
