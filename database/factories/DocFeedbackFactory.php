<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\DocFeedback;
use App\Models\DocPage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocFeedback>
 */
final class DocFeedbackFactory extends Factory
{
    protected $model = DocFeedback::class;

    public function definition(): array
    {
        return [
            'doc_page_id' => DocPage::factory(),
            'user_id' => null,
            'helpful' => fake()->boolean(),
            'comment' => null,
            'visitor_hash' => hash('sha256', fake()->uuid()),
        ];
    }
}
