<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Proposal;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\ProposalResource\Pages;
use Filament\Infolists\Components\Section as SectionInfolist;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProposalResource\RelationManagers;

class ProposalResource extends Resource
{
    protected static ?string $model = Proposal::class;

    protected static ?string $label = "Proposal";

    protected static ?string $modelLabel = 'Data Proposal';

    protected static ?string $pluralLabel = 'Proposal';

    protected static ?string $pluralModelLabel = 'Data Proposal';

    protected static ?string $navigationGroup = "Data Proposal";

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('proposal_name')
                    ->label('Proposal Name')
                    ->required(),
                Forms\Components\TextArea::make('proposal_objective')
                    ->label('Proposal Objective')
                    ->required(),
                Forms\Components\DatePicker::make('proposal_realization')
                    ->label('Proposal Realization')
                    ->required(),
                Forms\Components\TextInput::make('proposal_budget')
                    ->label('Proposal Budget')
                    ->numeric()
                    ->required(),
                Forms\Components\FileUpload::make('proposal_file')
                    ->label('Proposal File')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('No')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('proposal_name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('proposal_objective')
                    ->label('Objective')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('proposal_realization')
                    ->label('Realization')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('proposal_budget')
                    ->label('Budget')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('proposal_file')
                    ->label('File')
                    ->url(fn($record) => route('download.proposal', ['file' => $record->proposal_file]))
                    ->openUrlInNewTab()
                    ->formatStateUsing(fn($state) => 'Download')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('proposal_status')
                    ->label('Status')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('proposal_status')
                    ->label('Status')
                    ->options([
                        'PENDING' => 'PENDING',
                        'APPROVED' => 'APPROVED',
                        'REJECTED' => 'REJECTED',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => auth()->user()->isWakil()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('APPROVED')
                        ->label('APPROVED')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update([
                                    'proposal_status' => 'APPROVED',
                                    'proposal_approver_id' => auth()->id(),
                                ]);
                            }
                        })
                        ->requiresConfirmation()
                        ->color('success')
                        ->visible(fn($records) => auth()->user()->isDireksi()),
                    Tables\Actions\BulkAction::make('REJECTED')
                        ->label('REJECTED')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update([
                                    'proposal_status' => 'REJECTED',
                                    'proposal_approver_id' => auth()->id(),
                                ]);
                            }
                        })
                        ->requiresConfirmation()
                        ->color('danger')
                        ->visible(fn($records) => auth()->user()->isDireksi()),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationLabel(): string
    {
        return static::$label;
    }

    public static function getPluralLabel(): string
    {
        return static::$pluralLabel;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProposals::route('/'),
            'create' => Pages\CreateProposal::route('/create'),
            'edit' => Pages\EditProposal::route('/{record}/edit'),
            'view' => Pages\ViewProposal::route('/{record}'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                SectionInfolist::make('Data Proposal')
                    ->schema([
                        Fieldset::make('Informasi Utama')
                            ->schema([
                                TextEntry::make('proposal_name')
                                    ->label('Proposal Name')
                                    ->html()
                                    ->color('secondary'),
                                TextEntry::make('proposal_objective')
                                    ->label('Proposal Objective')
                                    ->html()
                                    ->color('secondary'),
                                TextEntry::make('proposal_realization')
                                    ->label('Proposal Realization')
                                    ->html()
                                    ->color('secondary'),
                                TextEntry::make('proposal_budget')
                                    ->label('Proposal Budget')
                                    ->html()
                                    ->color('secondary'),
                                // TextEntry::make('proposal_file')
                                //     ->label('Proposal File')
                                //     ->html()
                                //     ->color('secondary'),
                            ])->columns(2),
                    ]),

                SectionInfolist::make('Status Proposal')
                    ->schema([
                        Fieldset::make('Informasi Utama')
                            ->schema([
                                TextEntry::make('proposal_status')
                                    ->label('Proposal Status')
                                    ->html()
                                    ->color('secondary'),
                                TextEntry::make('approver.name')
                                    ->label('Proposal Name')
                                    ->html()
                                    ->color('secondary'),
                                TextEntry::make('initiator.name')
                                    ->label('Proposal Objective')
                                    ->html()
                                    ->color('secondary'),
                            ])->columns(3),
                    ]),
            ]);
    }

    //     public static function getEloquentQuery(): Builder
// {
//     $user = auth()->user();

    //     if ($user->role === 'WAKIL') {
//         return parent::getEloquentQuery()
//             ->whereHas('initiator', function ($query) use ($user) {
//                 $query->where('proposal_initiator_id', $user->id);
//             });
//     }

    //     return parent::getEloquentQuery();
// }
}
