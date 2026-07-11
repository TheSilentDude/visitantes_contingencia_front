<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    /**
     * Boot the trait
     */
    protected static function bootAuditable()
    {
        // Log creation
        static::created(function ($model) {
            AuditLog::createLog(
                'created',
                $model,
                self::getModelName($model) . " creado",
                null,
                $model->getAttributes()
            );
        });

        // Log updates
        static::updated(function ($model) {
            $changes = $model->getChanges();
            unset($changes['updated_at'], $changes['update_at']);
            
            if (!empty($changes)) {
                AuditLog::createLog(
                    'updated',
                    $model,
                    self::getModelName($model) . " actualizado",
                    $model->getOriginal(),
                    $changes
                );
            }
        });

        // Log deletion
        static::deleted(function ($model) {
            AuditLog::createLog(
                'deleted',
                $model,
                self::getModelName($model) . " eliminado",
                $model->getAttributes(),
                null
            );
        });
    }

    /**
     * Get friendly model name
     */
    private static function getModelName($model)
    {
        $className = class_basename($model);
        
        $names = [
            'Usuario' => 'Usuario',
            'Visitante' => 'Visitante',
            'CarnetVisitante' => 'Carnet de Visitante',
            'User' => 'Usuario del Sistema',
            'Cargo' => 'Cargo',
            'Departamento' => 'Departamento'
        ];
        
        return $names[$className] ?? $className;
    }
}
