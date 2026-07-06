<?php

namespace App\Filament\Resources\BenefitSlideResource\Pages;

use App\Filament\Resources\BenefitSlideResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBenefitSlides extends ListRecords
{
    protected static string $resource = BenefitSlideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
