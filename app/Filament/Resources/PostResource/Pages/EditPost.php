<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function authorizeAccess(): void
    {
        $user = Auth::user();
        $post = $this->getRecord();

        if (! $user instanceof User) {
            abort(403, 'Unauthorized to edit this post.');
        }

        // Non-admin users can only edit their own posts
        if (! $user->isAdmin() && $post->user_id !== $user->id) {
            abort(403, 'Unauthorized to edit this post.');
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return $data;
        }

        // Non-admin users can't change the author of a post
        if (! $user->isAdmin()) {
            $data['user_id'] = $this->getRecord()->user_id;
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
