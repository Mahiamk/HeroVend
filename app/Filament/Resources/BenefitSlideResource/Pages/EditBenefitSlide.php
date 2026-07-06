<?php

namespace App\Filament\Resources\BenefitSlideResource\Pages;

use App\Filament\Resources\BenefitSlideResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBenefitSlide extends EditRecord
{
    protected static string $resource = BenefitSlideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->record->moveToSortOrder((int) $this->record->sort_order);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
