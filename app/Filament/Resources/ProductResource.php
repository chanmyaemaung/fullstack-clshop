<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?string $navigationGroup = 'Shop';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Product Information')->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                        TextInput::make('slug')
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->maxLength(255)
                            ->unique(Product::class, 'slug', ignoreRecord: true),

                        MarkdownEditor::make('description')
                            ->nullable()
                            ->columnSpanFull()
                            ->fileAttachmentsDirectory('products'),
                    ]),

                    Section::make('Images')->schema([
                        FileUpload::make('images')
                            ->directory('products')
                            ->multiple()
                            ->image()
                            ->acceptedFileTypes(['image/*'])
                            ->reorderable()
                            ->maxFiles(10),
                    ]),
                ])->columnSpan(2),

                Group::make()->schema([
                    Section::make("Pricing")->schema([
                        TextInput::make('price')
                            ->required()
                            ->prefix('$')
                            ->type('number')
                            ->step('0.01'),
                    ]),

                    Section::make("Associations")->schema([
                        Select::make('category_id')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->relationship('category', 'name'),

                        Select::make('brand_id')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->relationship('brand', 'name'),

                    ]),

                    Section::make("Status")->schema([
                        Toggle::make('is_active')
                            ->required()
                            ->default(true),

                        Toggle::make('is_featured')
                            ->required()
                            ->default(false),

                        Toggle::make('in_stock')
                            ->required()
                            ->default(true),

                        Toggle::make('on_sale')
                            ->required()
                            ->default(true),
                    ]),
                ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('brand.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('price')
                    ->money("USD")
                    ->searchable()
                    ->sortable(),

                IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->sortable(),

                IconColumn::make('in_stock')
                    ->boolean()
                    ->label('In Stock')
                    ->sortable(),

                IconColumn::make('on_sale')
                    ->boolean()
                    ->label('On Sale')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->options(fn () => \App\Models\Category::pluck('name', 'id')->toArray()),

                SelectFilter::make('brand_id')
                    ->relationship('brand', 'name')
                    ->options(fn () => \App\Models\Brand::pluck('name', 'id')->toArray()),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ForceDeleteAction::make()
                ])
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
