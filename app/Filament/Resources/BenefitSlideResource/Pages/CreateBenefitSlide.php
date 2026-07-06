<?php

namespace App\Filament\Resources\BenefitSlideResource\Pages;

use App\Filament\Resources\BenefitSlideResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBenefitSlide extends CreateRecord
{
    protected static string $resource = BenefitSlideResource::class;

    protected function afterCreate(): void
    {
        $this->record->moveToSortOrder((int) $this->record->sort_order);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
