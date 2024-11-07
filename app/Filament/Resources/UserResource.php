<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $label = "User";

    protected static ?string $modelLabel = 'Data User';

    protected static ?string $pluralLabel = 'User';

    protected static ?string $pluralModelLabel = 'Data User';

    protected static ?string $navigationGroup = "Settings";

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->required()
                    ->email(),
                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->required()
                    ->live()
                    ->minLength(8)
                    ->rule('regex:/^(?=.*[!@#$%^&*(),.?":{}|<>])(?=.*\d.*\d).{8,}$/') // 8 karakter, 2 angka, 1 simbol
                    ->validationMessages([
                        'required' => 'Password tidak boleh kosong',
                        'min' => 'Password harus memiliki minimal 8 karakter',
                        'regex' => 'Password harus mengandung minimal 8 karakter, setidaknya 2 angka, dan 1 simbol',
                    ])
                    ->afterStateUpdated(function ($state, callable $set) {
                        $strongPattern = '/^(?=.*\d.*\d)(?=(?:.*[!@#$%^&*(),.?":{}|<>]){2,}).{8,}$/'; // 8 karakter, 2 angka, lebih dari 1 simbol
                        $weakPattern = '/^(?=.*\d.*\d)(?=(?:.*[!@#$%^&*(),.?":{}|<>]){1}).{8,}$/';    // 8 karakter, 2 angka, hanya 1 simbol

                        if (preg_match($strongPattern, $state)) {
                            $set('password_strength', 'Strong');
                        } elseif (preg_match($weakPattern, $state)) {
                            $set('password_strength', 'Weak');
                        } else {
                            $set('password_strength', 'Unqualified');
                        }
                    }),
                Forms\Components\TextInput::make('password_strength')
                    ->label('Password Strength')
                    ->disabled(),
                Forms\Components\Select::make('role')
                    ->label('Role')
                    ->required()
                    ->options(User::ROLES),
                Forms\Components\Toggle::make('status_akun')
                    ->label('Status Akun'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('No')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('status_akun')
                    ->label('Status Akun')
                    ->beforeStateUpdated(function ($record, $state) {
                    })
                    ->afterStateUpdated(function ($record, $state) {
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        Fieldset::make('User Information')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Nama :')
                                    ->icon('heroicon-m-user')
                                    ->color('secondary'),
                                TextEntry::make('email')
                                    ->label('Email :')
                                    ->icon('heroicon-m-envelope')
                                    ->color('secondary')
                                    ->copyable()
                                    ->copyMessage('Copied!')
                                    ->copyMessageDuration(1500),
                                TextEntry::make('role')
                                    ->label('Role :')
                                    ->icon('heroicon-m-information-circle')
                                    ->color('secondary'),
                            ])->columns(2),
                    ])
            ]);
    }
}
