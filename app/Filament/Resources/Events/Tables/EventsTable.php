<?php

namespace App\Filament\Resources\Events\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('guest_messages_count')
                    ->counts('guestMessages')
                    ->label('Messages')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('display')
                    ->label('Display')
                    ->icon('heroicon-o-tv')
                    ->url(fn (\App\Models\Event $record): string => $record->getDisplayUrl())
                    ->openUrlInNewTab(),
                Action::make('feedback')
                    ->label('Feedback')
                    ->icon('heroicon-o-chat-bubble-bottom-center-text')
                    ->url(fn (\App\Models\Event $record): string => $record->getFeedbackUrl())
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
