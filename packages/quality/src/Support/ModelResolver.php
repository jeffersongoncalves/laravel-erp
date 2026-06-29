<?php

namespace JeffersonGoncalves\Erp\Quality\Support;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class ModelResolver
{
    /** @var array<string, string> */
    protected static array $cache = [];

    /** @return class-string<Model> */
    public static function qualityGoal(): string
    {
        return static::resolve('quality_goal');
    }

    /** @return class-string<Model> */
    public static function qualityGoalObjective(): string
    {
        return static::resolve('quality_goal_objective');
    }

    /** @return class-string<Model> */
    public static function qualityProcedure(): string
    {
        return static::resolve('quality_procedure');
    }

    /** @return class-string<Model> */
    public static function qualityProcedureProcess(): string
    {
        return static::resolve('quality_procedure_process');
    }

    /** @return class-string<Model> */
    public static function qualityInspectionTemplate(): string
    {
        return static::resolve('quality_inspection_template');
    }

    /** @return class-string<Model> */
    public static function qualityInspectionTemplateParameter(): string
    {
        return static::resolve('quality_inspection_template_parameter');
    }

    /** @return class-string<Model> */
    public static function qualityInspection(): string
    {
        return static::resolve('quality_inspection');
    }

    /** @return class-string<Model> */
    public static function qualityInspectionReading(): string
    {
        return static::resolve('quality_inspection_reading');
    }

    /** @return class-string<Model> */
    public static function nonConformance(): string
    {
        return static::resolve('non_conformance');
    }

    /** @return class-string<Model> */
    public static function qualityAction(): string
    {
        return static::resolve('quality_action');
    }

    /** @return class-string<Model> */
    public static function qualityActionResolution(): string
    {
        return static::resolve('quality_action_resolution');
    }

    /** @return class-string<Model> */
    public static function qualityReview(): string
    {
        return static::resolve('quality_review');
    }

    /** @return class-string<Model> */
    public static function qualityReviewObjective(): string
    {
        return static::resolve('quality_review_objective');
    }

    /**
     * @return class-string
     *
     * @throws InvalidArgumentException
     */
    protected static function resolve(string $key): string
    {
        if (isset(static::$cache[$key])) {
            return static::$cache[$key];
        }

        /** @var class-string|null $model */
        $model = config("erp-quality.models.{$key}");

        if (! $model || ! class_exists($model)) {
            throw new InvalidArgumentException(
                "Model class for [{$key}] does not exist: {$model}"
            );
        }

        return static::$cache[$key] = $model;
    }

    public static function flushCache(): void
    {
        static::$cache = [];
    }
}
