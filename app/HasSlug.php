<?php

namespace App;

use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug()
    {
        static::creating(function ($model) {
            $model->generateSlugOnCreate();
        });

        static::updating(function ($model) {
            $model->generateSlugOnUpdate();
        });
    }

    protected function generateSlugOnCreate()
    {
        // اگر مدل اسلاگ دارد و هنوز مقدار ندارد → بساز
        if (empty($this->slug) && ! empty($this->title)) {
            $this->slug = $this->generateUniqueSlug($this->title);
        }
    }

    protected function generateSlugOnUpdate()
    {
        // فقط اگر title تغییر کرده
        if ($this->isDirty('title') && ! empty($this->title)) {
            $this->slug = $this->generateUniqueSlug($this->title, $this->id);
        }
    }

    protected function generateUniqueSlug(string $value, $exceptId = null)
    {
        $slug = Str::slug($value);
        $original = $slug;
        $count = 1;

        while (
            $this->newQuery()
                ->where('slug', $slug)
                ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
                ->exists()
        ) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }
}
