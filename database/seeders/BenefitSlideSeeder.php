<?php

namespace Database\Seeders;

use App\Models\BenefitSlide;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BenefitSlideSeeder extends Seeder
{
    public function run(): void
    {
        $slides = [
            [
                'title' => 'LOW CAPITAL',
                'body_text' => 'We supply machines with zero up-front hardware investments or monthly operational lease commitments.',
                'sort_order' => 1,
            ],
            [
                'title' => 'ALWAYS ON',
                'body_text' => 'Our telemetry software monitors inventory thresholds, vending errors, and continuous power states in real-time.',
                'sort_order' => 2,
            ],
            [
                'title' => 'EASY TO SCALE',
                'body_text' => 'Start with one machine, then simply place more in new locations as your profits grow.',
                'sort_order' => 3,
            ],
            [
                'title' => 'LOW RISK',
                'body_text' => "If a location isn't performing well, just move the machine to a better spot.",
                'sort_order' => 4,
            ],
        ];

        DB::transaction(function () use ($slides) {
            foreach ($slides as $slide) {
                BenefitSlide::updateOrCreate(['title' => $slide['title']], $slide);
            }
        });
    }
}
