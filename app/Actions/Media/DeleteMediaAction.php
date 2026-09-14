<?php

declare(strict_types=1);

namespace App\Actions\Media;

use App\Models\Media;

/**
 * Menghapus media dengan satu syarat: file tidak sedang dipakai siapa pun.
 */
final class DeleteMediaAction
{
    /**
     * @return bool `false` kalau media masih terpasang di post/halaman docs.
     */
    public function handle(Media $media): bool
    {
        if ($media->isInUse()) {
            return false;
        }

        // Soft delete: file tetap ada di disk sampai di-force delete, sehingga
        // media yang keliru terhapus masih bisa dipulihkan.
        $media->delete();

        return true;
    }
}
