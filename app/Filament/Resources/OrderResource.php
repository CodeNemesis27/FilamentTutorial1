<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatusEnum;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationGroup = 'Shop';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Order Details')->schema([
                        TextInput::make('number')
                            ->default('OR-' . random_int(100000, 9999999))
                            ->disabled()
                            ->dehydrated()
                            ->required(),

                        Select::make('customer_id')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('type')
                            ->options([
                                'pending' => OrderStatusEnum::PENDING->value,
                                'processing' => OrderStatusEnum::PROCESSING->value,
                                'completed' => OrderStatusEnum::COMPLETED->value,
                                'declined' => OrderStatusEnum::DECLINED->value,
                            ])
                            ->columnSpanFull()
                            ->required(),

                        MarkdownEditor::make('notes')
                            ->columnSpanFull()

                    ])->columns(2),

                    Step::make('Order Items')
                        ->schema([
                            Repeater::make('items')
                                ->relationship()
                                ->schema([
                                    Select::make('product_id')
                                        ->label('Product')
                                        ->required()
                                        ->reactive()
                                        ->afterStateUpdated(fn($state, Forms\Set $set) =>
                                        $set('unit_price', Product::find($state)?->price ?? 0))
                                        ->options(Product::query()->pluck('name', 'id')),

                                    TextInput::make('quantity')
                                        ->numeric()
                                        ->default(1)
                                        ->dehydrated()
                                        ->live()
                                        ->required(),

                                    TextInput::make('unit_price')
                                        ->label('Unit Price')
                                        ->disabled()
                                        ->dehydrated()
                                        ->numeric(),

                                    Placeholder::make('total_price')
                                        ->label('Total Price')
                                        ->content(function ($get) {
                                            return $get('quantity') * $get('unit_price');
                                        })
                                ])->columns(4)
                        ])
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Order Date')
                    ->date()
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
