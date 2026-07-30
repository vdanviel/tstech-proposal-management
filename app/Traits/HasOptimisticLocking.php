<?php

namespace App\Traits;

use Exception;

trait HasOptimisticLocking
{

    //nome da coluna de versão da tabela
    protected string $versionColumnName = 'version';

    public static function bootHasOptimisticLocking(): void
    {
        static::updating(function ($model) {// ao atualizar model essa função vai acionar

            $column = $model->versionColumnName;

            $originalVersion = (int) ($model->getOriginal($column) ?? 0);

            $attributesToUpdate = $model->getDirty();//recupera os campos que estão sendo atualizados
            unset($attributesToUpdate[$column]);//remove coluna version dos atualizados se o processo do codigo tentar atualizar (pois só vai ser atualizada aqui trait)

            //faz o update com a trava de concorrencia
            $affected = $model->newQueryWithoutScopes()//começa novo builder enxuto, sem escopos laravel
                ->where($model->getKeyName(), $model->getKey())//where id = num
                ->where($column, $originalVersion)//where version = num
                ->update(array_merge(
                    $attributesToUpdate,
                    [$column => $originalVersion + 1]
                ));//lista de updates efetivos

            //se não afetou
            if ($affected === 0) {
                throw new Exception("O recurso já foi alterado.");
            }

            $model->setAttribute($column, $originalVersion + 1);

            return false;
        });
    }

}
