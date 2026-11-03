<?php

namespace App\Filament\Resources\Rankings\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RankingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('General')->schema([
                Select::make('category_id')->label('Categoría')->relationship('category', 'name_plural')->required()->preload(),
                Select::make('municipality_id')->label('Municipio')->relationship('municipality', 'name')->searchable()->preload(),
                TextInput::make('title')->label('Título')->required(),
                TextInput::make('slug')->required(),
                TextInput::make('h1')->label('H1')->required(),
                Select::make('author_id')->label('Autor')->relationship('author', 'name')->required()->preload(),
                Select::make('status')->options(['draft' => 'Borrador', 'published' => 'Publicado'])->default('draft')->required(),
                DateTimePicker::make('reviewed_at')->label('Revisado'),
            ])->columns(2),
            Section::make('Contenido')->schema([
                Textarea::make('answer')->label('Respuesta directa')->required()->rows(4)->columnSpanFull(),
                RichEditor::make('intro')->label('Introducción')->required()->columnSpanFull(),
                TagsInput::make('criteria')->label('Criterios')->required()->minItems(3)->maxItems(6),
                Repeater::make('faq')->label('Preguntas frecuentes')->schema([TextInput::make('q')->label('Pregunta')->required(), Textarea::make('a')->label('Respuesta')->required()])->minItems(4)->maxItems(8)->columnSpanFull(),
                Textarea::make('closing_text')->label('Cierre para empresas')->required()->columnSpanFull(),
            ]),
            Section::make('SEO y API')->schema([
                TextInput::make('primary_keyword')->label('Keyword principal')->required()->unique(ignoreRecord: true),
                TagsInput::make('secondary_keywords')->label('Keywords secundarias')->minItems(3)->maxItems(8),
                TextInput::make('seo_title')->label('Title')->maxLength(60),
                TextInput::make('seo_description')->label('Description')->maxLength(160),
                FileUpload::make('og_image_path')->label('Imagen Open Graph')->image()->directory('rankings/og'),
                DateTimePicker::make('published_at')->label('Publicado'),
            ])->columns(2),
        ]);
    }
}
