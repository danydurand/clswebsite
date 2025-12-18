<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait HasRelatedRecords
{
    /**
     * Check if the model has any related records in its hasMany or hasManyThrough relationships.
     *
     * @return bool
     */
    public function hasRelatedRecords(): bool
    {
        // Get all hasMany and hasManyThrough relationships of the model
        $relationships = $this->getHasManyRelationships();

        // info(print_r($relationships, true));

        foreach ($relationships as $relationship) {
            // Check if the relationship should be excluded due to cascade delete
            // info($relationship.': '.$this->isCascadeDelete($relationship));

            if ($this->isCascadeDelete($relationship)) {
                continue;
            }

            // Check if there are related records
            if ($this->$relationship()->exists()) {
                // info($relationship);
                return true;
            }
        }

        return false;
    }

    /**
     * Get all hasMany and hasManyThrough relationships of the model.
     *
     * @return array
     */
    protected function getHasManyRelationships(): array
    {
        $relationships = [];

        // Get the model's methods
        $methods = get_class_methods($this);

        foreach ($methods as $method) {
            $reflectionMethod = new \ReflectionMethod($this, $method);

            if (!$reflectionMethod->isPublic()) {
                continue;
            }

            $returnType = $reflectionMethod->getReturnType();

            if ($returnType && (
                $returnType->getName() === \Illuminate\Database\Eloquent\Relations\HasMany::class ||
                $returnType->getName() === \Illuminate\Database\Eloquent\Relations\HasManyThrough::class
            )) {
                $relationships[] = $method;
            }
        }

        return $relationships;
    }

    /**
     * Check if the relationship is controlled by cascade delete.
     *
     * @param string $relationship
     * @return bool
     */
    protected function isCascadeDelete(string $relationship): bool
    {
        // Get the related model instance
        $relatedModel = $this->$relationship()->getRelated();

        // Get the foreign key of the relationship
        if ($this->$relationship() instanceof \Illuminate\Database\Eloquent\Relations\HasManyThrough) {
            $foreignKey = $this->$relationship()->getForeignKeyName();
        } else {
            // Extract just the column name without the table prefix
            $qualifiedKey = $this->$relationship()->getQualifiedForeignKeyName();
            $foreignKey = substr($qualifiedKey, strpos($qualifiedKey, '.') + 1);
        }

        // Get the table name of the related model
        $table = $relatedModel->getTable();

        // Query PostgreSQL system catalogs to check for cascade delete
        $result = DB::select("
            SELECT rc.delete_rule
            FROM information_schema.referential_constraints rc
            JOIN information_schema.key_column_usage kcu
                ON rc.constraint_name = kcu.constraint_name
            WHERE kcu.table_name = ?
              AND kcu.column_name = ?
              AND rc.delete_rule = 'CASCADE'
        ", [$table, $foreignKey]);

        return !empty($result);
    }

    /**
     * Get the names of related tables in an HTML unordered list.
     *
     * @return string
     */
    public function getRelatedTablesHtml(): string
    {
        $relationships = $this->getHasManyRelationships();
        $relatedTables = [];

        foreach ($relationships as $relationship) {
            // Check if the relationship should be excluded due to cascade delete
            if ($this->isCascadeDelete($relationship)) {
                continue;
            }

            // Get the related model instance
            if ($this->$relationship()->exists()) {
                $relatedModel = $this->$relationship()->getRelated();
                $relatedTables[] = class_basename($relatedModel);
                // $relatedTables[] = $relatedModel->getTable();
            }
        }

        $relatedTables = array_unique($relatedTables);

        // Generate HTML unordered list
        $html = '<ul>';
        foreach ($relatedTables as $table) {
            $html .= '<li>&nbsp;&nbsp;*&nbsp;' . htmlspecialchars(Str::ucfirst($table), ENT_QUOTES, 'UTF-8') . '</li>';
        }
        $html .= '</ul>';

        return $html;
    }
}
