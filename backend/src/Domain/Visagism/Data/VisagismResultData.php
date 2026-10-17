<?php

declare(strict_types=1);

namespace Domain\Visagism\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

final class VisagismResultData extends Data
{
    /**
     * @param  DataCollection<int, HaircutSuggestionData>  $suggestions
     * @param  DataCollection<int, ProductRecommendationData>  $upsell_products
     */
    public function __construct(
        #[Required, StringType, In(['oval', 'square', 'round', 'rectangular', 'inverted_triangle', 'diamond'])]
        public readonly string $face_shape,
        #[Required, StringType, Max(500)]
        public readonly string $face_geometry_summary,
        #[Required, StringType, In(['straight', 'wavy', 'curly', 'coily'])]
        public readonly string $hair_type,
        #[Required, DataCollectionOf(HaircutSuggestionData::class), Min(1)]
        public readonly DataCollection $suggestions,
        #[Required, DataCollectionOf(ProductRecommendationData::class)]
        public readonly DataCollection $upsell_products,
    ) {}
}
