<?php

namespace App\Forms;

use Filament\Forms;
use Illuminate\Database\Eloquent\Model;
use Squire\Models\Country;
class AddressForm extends Forms\Components\Field
{
    protected string $view = 'forms::components.group';

    public $relationship = null;

    public function relationship(string | callable $relationship): static
    {
        $this->relationship = $relationship;

        return $this;
    }

    public function saveRelationships(): void
    {
        $state = $this->getState();
        $record = $this->getRecord();
        $relationship = $record->{$this->getRelationship()}();

        if (in_array(!null, $state)) {
            if ($address = $relationship->first()) {
                $address->update($state);
            } else {
                $relationship->updateOrCreate($state);
            }
        }


        $record->touch();
    }

    public function getChildComponents(): array
    {
        return [
            Forms\Components\TextInput::make('street')
                ->label('Street address'),

            Forms\Components\TextInput::make('city')
                ->label('City'),

            Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\TextInput::make('state')
                        ->label('State / Province'),
                    Forms\Components\TextInput::make('zip')
                        ->label('Zip / Postal code'),
                ]),

            Forms\Components\Grid::make()
                ->schema([
                    Forms\Components\Select::make('country')
                        ->searchable()
                        ->getSearchResultsUsing(fn (string $query) => Country::where('name', 'like', "%{$query}%")->pluck('name', 'id'))
                        ->getOptionLabelUsing(fn ($value): ?string => Country::find($value)?->getAttribute('name'))
                        ->columnSpanFull()
                ]),
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (AddressForm $component, ?Model $record) {
            $address = $record?->getRelationValue($this->getRelationship());

            $component->state($address ? $address->toArray() : [
                'country' => null,
                'street' => null,
                'city' => null,
                'state' => null,
                'zip' => null,
            ]);
        });

        $this->dehydrated(false);
    }

    public function getRelationship(): string
    {
        return $this->evaluate($this->relationship) ?? $this->getName();
    }
}