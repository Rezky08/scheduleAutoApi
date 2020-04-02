<?php

namespace App\Observers;

use Exception;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ReflectionClass;
use ReflectionMethod;

class GlobalObserver
{
    public function updated($model)
    {
        // update kode_prodi related data
        $original = $model->getOriginal();
        $changes = $model->getChanges();
        $params = $model->relatedModel['update'];
        while ($related_model = array_shift($params)) {
            $modelName = array_shift($related_model);
            $foreign_key = array_shift($related_model);
            $local_key = array_shift($related_model);

            if (!$local_key) {
                $local_key = $foreign_key;
            }

            if (collect($changes)->has($local_key)) {
                $modelName = $modelName::where($foreign_key, $original[$local_key])->withTrashed()->get();
                foreach ($modelName as $key => $item) {
                    $item->$foreign_key = $changes[$local_key];
                    $item->save();
                }
            }
        }
    }

    public function deleted($model)
    {
        $params = $model->relatedModel['delete'];
        while ($method = array_shift($params)) {
            $model->$method()->delete();
        }
    }
}
