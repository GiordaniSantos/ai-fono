<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Image\Enums\Fit;

abstract class AbstractBaseModel extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public const string COLLECTION_IMAGES = 'imagens';
    public const string COLLECTION_DOCS   = 'documentos';
    public const string COLLECTION_AUDIOS = 'audios';
    public const string COLLECTION_VIDEOS  = 'videos';
    public const string COLLECTION_ANEXO  = 'anexo';
    public const string COLLECTION_FOTO  = 'foto';

    public const string NO_FILE_PATH = '/images/indisponivel.png';

    const VERSION_PREVIEW = 'preview';
    const VERSION_MID   = 'mid';
    const VERSION_SMALL = 'small';
    const VERSION_THUMB = 'thumb';

    public $imageVersions = [
        // self::VERSION_LARGE => [2200, 1200],
        self::VERSION_MID   => [1200, 600],
        self::VERSION_SMALL => [600, 300],
        self::VERSION_THUMB => [200, 100]
    ];

    /**
     * Registro das conversões de imagem
    */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('preview')
            ->fit(Fit::Contain, 300, 300)
            ->format('webp')
            ->nonQueued();

        foreach ($this->imageVersions as $name => $dimensions) {
            $this->addMediaConversion($name)
                ->fit(Fit::Contain, $dimensions[0], $dimensions[1])
                ->format('webp')
                ->quality(90)
                ->sharpen(10)
                ->optimize()
                ->nonQueued();
        }
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::COLLECTION_IMAGES)
            ->useFallbackUrl(asset(self::NO_FILE_PATH))
            ->useFallbackPath(public_path(self::NO_FILE_PATH));
    }

    public function getImagens()
    {
        return $this->getMedia(self::COLLECTION_IMAGES);
    }

    public function getDocumentos()
    {
        return $this->getMedia(self::COLLECTION_DOCS);
    }

    public function getAudios()
    {
        return $this->getMedia(self::COLLECTION_AUDIOS);
    }

    public function scopeList($query, $label = 'nome', $key = 'id', $filters = null)
    {
        if (is_callable($filters)) {
            $filters($query);
        }

        return $query->orderBy($label)->pluck($label, $key);
    }

    public function scopeBuscar(Builder $query, $value, $attributes)
    {
        if (is_null($value) || $value === '') {
            return $query;
        }

        $normalizedValue = Str::slug($value, '');

        $attributes = is_array($attributes) ? $attributes : [$attributes];

        $columnsConcat = implode(" || ' ' || ", $attributes);

        $from = 'áéíóúàèìòùãõâêîôôäëïöüçÁÉÍÓÚÀÈÌÒÙÃÕÂÊÎÔÔÄËÏÖÜÇ';
        $to   = 'aeiouaeiouaoaeiooaeioucAEIOUAEIOUAOAEIOOAEIOUC';

        $rawSql = "REGEXP_REPLACE(
            TRANSLATE(LOWER({$columnsConcat}), '{$from}', '{$to}'),
            '[^a-z0-9]+', '', 'g'
        )";

        return $query->whereRaw("{$rawSql} LIKE ?", ["%{$normalizedValue}%"]);
    }

}
