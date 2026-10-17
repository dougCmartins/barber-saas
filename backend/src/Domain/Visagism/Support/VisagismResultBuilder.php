<?php

declare(strict_types=1);

namespace Domain\Visagism\Support;

use Domain\Visagism\Data\HaircutSuggestionData;
use Domain\Visagism\Data\ProductRecommendationData;
use Domain\Visagism\Data\VisagismResultData;
use Spatie\LaravelData\DataCollection;

final class VisagismResultBuilder
{
    private const FACE_SUMMARIES = [
        'oval' => 'Proporções equilibradas com testa e mandíbula harmoniosas.',
        'square' => 'Mandíbula marcada e testa larga.',
        'round' => 'Contornos suaves com largura e comprimento próximos.',
        'rectangular' => 'Rosto alongado com linha da mandíbula definida.',
        'inverted_triangle' => 'Testa mais larga com mandíbula afilada.',
        'diamond' => 'Maçãs do rosto marcadas com testa e queixo mais estreitos.',
    ];

    public static function build(string $faceShape, string $hairType): VisagismResultData
    {
        $isCoily = in_array($hairType, ['curly', 'coily'], true);

        $suggestions = [
            new HaircutSuggestionData(
                id: 1,
                name: 'Corte Clássico Texturizado',
                match_percentage: 98,
                justification: 'A texturização no topo e as laterais mais curtas suavizam os ângulos fortes da sua mandíbula.',
                image_url: null,
                service_id: 1,
                price: 65.0,
            ),
            new HaircutSuggestionData(
                id: 2,
                name: 'Fade Médio',
                match_percentage: 94,
                justification: 'O volume vertical no topo cria alongamento e equilibra a largura do seu maxilar.',
                image_url: null,
                service_id: 2,
                price: 55.0,
            ),
            new HaircutSuggestionData(
                id: 3,
                name: 'Side Part Clássico',
                match_percentage: 89,
                justification: 'A partitura lateral estrutura a testa larga e reforça a simetria do rosto.',
                image_url: null,
                service_id: 3,
                price: 60.0,
            ),
        ];

        $upsellProducts = $isCoily
            ? [
                new ProductRecommendationData(
                    id: 1,
                    name: 'Leave-in Definição',
                    price: 59.0,
                    microcopy: 'Mantenha a definição e hidratação dos fios crespos.',
                    image_url: null,
                    service_id: 4,
                ),
                new ProductRecommendationData(
                    id: 2,
                    name: 'Óleo para Barba',
                    price: 45.0,
                    microcopy: 'O contorno da barba é o que define a harmonia do seu visual no dia a dia.',
                    image_url: null,
                    service_id: 5,
                ),
            ]
            : [
                new ProductRecommendationData(
                    id: 1,
                    name: 'Pomada Modeladora',
                    price: 49.0,
                    microcopy: 'Essencial para criar volume e manter o acabamento seco do degradê.',
                    image_url: null,
                    service_id: 4,
                ),
            ];

        return new VisagismResultData(
            face_shape: $faceShape,
            face_geometry_summary: self::FACE_SUMMARIES[$faceShape] ?? self::FACE_SUMMARIES['square'],
            hair_type: $hairType,
            suggestions: new DataCollection(HaircutSuggestionData::class, $suggestions),
            upsell_products: new DataCollection(ProductRecommendationData::class, $upsellProducts),
        );
    }
}
