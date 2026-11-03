<?php

namespace App\Filament\Resources\Companies\Schemas;

use App\Enums\CompanyStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('General')->schema([
                TextInput::make('name')->label('Nombre comercial')->required()->maxLength(120),
                TextInput::make('slug')->helperText('Se genera automáticamente si se deja vacío.'),
                TextInput::make('website_url')->label('Web')->url(),
                TextInput::make('cif')->label('CIF')->maxLength(9),
                Select::make('status')->options(CompanyStatus::class)->default('draft')->required(),
                Toggle::make('is_verified')->label('Ficha verificada'),
                Select::make('categories')->relationship('categories', 'name_plural')->multiple()->maxItems(5)->preload()->required(),
                Select::make('municipality_id')->label('Municipio')->relationship('municipality', 'name')->searchable()->preload()->required(),
            ])->columns(2),
            Section::make('Contenido')->schema([
                TextInput::make('short_description')->label('Descripción corta')->required()->maxLength(160),
                Textarea::make('description')->label('Descripción')->required()->rows(8)->columnSpanFull(),
                TagsInput::make('services')->label('Servicios')->maxItems(12),
                TagsInput::make('sectors')->label('Sectores')->maxItems(12),
            ]),
            Section::make('Contacto')->schema([
                TextInput::make('phone_public')->label('Teléfono')->tel(),
                TextInput::make('email_public')->label('Email')->email(),
                TextInput::make('address')->label('Dirección'),
                TextInput::make('postal_code')->label('Código postal')->startsWith('04')->maxLength(5),
            ])->columns(2),
            Section::make('Imágenes')->schema([
                FileUpload::make('logo_path')->label('Logo')->image()->imageEditor()->directory('companies/logos'),
                FileUpload::make('cover_path')->label('Portada')->image()->imageEditor()->directory('companies/covers'),
                FileUpload::make('gallery')->label('Galería')->image()->multiple()->maxFiles(8)->directory('companies/gallery'),
            ])->columns(2),
            Section::make('Datos y SEO')->schema([
                TextInput::make('founded_year')->label('Año de fundación')->numeric()->minValue(1900)->maxValue(date('Y')),
                Select::make('team_size')->label('Equipo')->options(array_combine(['1', '2-5', '6-10', '11-25', '26-50', '50+'], ['1', '2-5', '6-10', '11-25', '26-50', '50+'])),
                TextInput::make('google_rating')->label('Valoración Google')->numeric()->minValue(0)->maxValue(5),
                TextInput::make('google_reviews_count')->label('Reseñas Google')->numeric(),
                TextInput::make('seo_title')->label('Title')->maxLength(60),
                TextInput::make('seo_description')->label('Description')->maxLength(160),
                Toggle::make('seo_noindex')->label('No indexar'),
                DateTimePicker::make('published_at')->label('Publicada'),
                DateTimePicker::make('reviewed_at')->label('Revisada'),
            ])->columns(2),
        ]);
    }
}
