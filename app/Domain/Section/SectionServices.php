<?php

namespace App\Domain\Section;

use App\Models\Bet;
use App\Models\Bank;
use App\Models\User;
use App\Models\Event;
use App\Models\Section;
use Filament\Forms\Form;
use App\Classes\PResponse;
use App\Services\AuthUser;
use Illuminate\Support\Str;
use App\Domain\Bet\BetTypeEnum;
use App\Domain\Bet\BetStatusEnum;
use App\Domain\User\UserTypeEnum;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Log;
use App\Models\FinancialTransaction;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Domain\Customer\CustomerServices;
use Filament\Forms\Components\ToggleButtons;
use App\Domain\FinancialTransaction\TrxTypeEnum;
use Filament\Forms\Components\Section as FormSection;

class SectionServices
{

    public static function getForm(bool $showSystem=true, ?Form $form)
    {
        $qty = $showSystem ? 5 : 4;
        $operation = $form === null ? 'create' : $form->getOperation();

        info('Operation: '.$operation);


        return [
            FormSection::make()->schema([
                Grid::make($qty)->schema([
                    Select::make('system_id')
                        ->relationship('system', 'name')
                        ->required()
                        ->visible($showSystem),
                    TextInput::make('name')
                        ->required()
                        ->maxLength(50)
                        ->columnSpan(2),
                    ToggleButtons::make('is_active')
                        ->label('Active?')
                        ->default(true)
                        ->inline()
                        ->required()
                        ->grouped()
                        ->options(yesNoOptions()),
                    TextInput::make('position')
                        ->numeric()
                        ->disabled($operation == 'create'),
                ])
            ])
        ];
    }



    public static function getPosition(int $systemId): string
    {
        return Section::bySystem($systemId)->max('position') + 1;
    }




}
