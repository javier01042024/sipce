<?php

namespace App\Observers;

use App\Helpers\BitacoraHelper;
use Illuminate\Database\Eloquent\Model;

class BitacoraObserver
{
    public function created(Model $model): void
    {
        BitacoraHelper::creado(
            $model->getTable(),
            $model->getKey(),
            $model->getAttributes()
        );
    }

    public function updated(Model $model): void
    {
        $dirty = $model->getDirty();
        $original = array_intersect_key($model->getOriginal(), $dirty);

        BitacoraHelper::actualizado(
            $model->getTable(),
            $model->getKey(),
            $original,
            $dirty
        );
    }

    public function deleted(Model $model): void
    {
        BitacoraHelper::eliminado(
            $model->getTable(),
            $model->getKey(),
            $model->getAttributes()
        );
    }
}
