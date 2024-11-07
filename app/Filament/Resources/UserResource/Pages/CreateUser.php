<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Js;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            ...(static::canCreateAnother() ? [$this->getCreateAnotherFormAction()] : []),
            $this->getCancelFormAction(),
        ];
    }

    protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->label('Save & View')
            ->submit('')
            ->requiresConfirmation()
            ->action(function () {
                $this->closeActionModal();
                $this->create();
            });
        // ->keyBindings(['mod+s']);
        // ->alpineClickHandler('if (confirm("Are you sure you want to save and view?")) { $event.target.closest("form").submit(); }');
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return Action::make('createAnother')
            ->label('Save & Create Another')
            ->submit('')
            ->requiresConfirmation()
            // ->action('createAnother')
            // ->keyBindings(['mod+shift+s'])
            ->requiresConfirmation()
            ->action(function () {
                $this->closeActionModal();
                $this->createAnother();
            })
            ->color('secondary');
        // ->alpineClickHandler('if (confirm("Are you sure you want to save and create another?")) { $event.target.closest("form").submit(); }');
    }

    protected function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->label('Cancel')
            ->alpineClickHandler('document.referrer ? window.history.back() : (window.location.href = ' . Js::from($this->previousUrl ?? static::getResource()::getUrl()) . ')')
            ->color('gray');
    }

    protected function handleRecordCreation(array $data): User
    {
        $dataUser = static::getModel()::create($data);

        $currentUser = Auth::user();

        if ($currentUser) {
            Notification::make()
                ->title('Ada Informasi Baru')
                ->body(' Data User berhasil disimpan ')
                ->color('primary')
                ->info()
                ->sendToDatabase([$currentUser])
                ->send();
        }

        return $dataUser;
    }
}
