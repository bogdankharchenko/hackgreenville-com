<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class EventOverview extends ChartWidget
{
    protected static ?string $heading = 'Events Last 30 + Next 60 days';

    protected static ?array $options = [
        'elements' => [
            'line' => [
                'tension' => 0.5,
            ],
        ],
        'scales' => [
            'y' => [
                'beginAtZero' => true,
                'ticks' => [
                    'stepSize' => 1
                ]
            ],
        ],
    ];

    protected function getData(): array
    {
        $events = Event::query()
            ->select([
                DB::raw('date_format(active_at, "%Y-%m-%d") as x'),
                DB::raw('count(id) as y'),
            ])
            ->whereBetween('active_at', [
                now()->subDays(30),
                now()->addDays(60),
            ])
            ->groupBy(DB::raw("date_format(active_at, '%Y-%m-%d')"))
            ->toBase()
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Events',
                    'data' => $events->pluck('y')->toArray(),
                ],
            ],
            'labels' => $events->pluck('x')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
