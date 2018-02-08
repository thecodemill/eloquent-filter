<?php

namespace TheCodeMill\EloquentFilter;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    /**
     * Scope a query to only include models resolving to all the given filter values.
     *
     * Eg. App\User::filter(['name' => 'Johnny'])->get();
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter(Builder $query, array $filters = [])
    {
        $filters = static::filters();

        foreach (static::validFilters($filters) as $attribute => $value) {
            $query->where(function ($query) use ($filterHandlers, $attribute, $value) {
                $filterHandlers[$attribute]($query, $value, $attribute);
            });
        }

        return $query;
    }

    /**
     * Return the values from a key => value array that may be filtered.
     *
     * This is a useful way to actually filter the filters! Most often used to append filter parameters to rendered
     * pagination links.
     *
     * Eg. $paginator->appends(App\Model::validFilters(request()->all()))->links()
     *
     * @param array $filters
     * @return array
     */
    public static function validFilters(array $filters = [])
    {
        $valid = [];

        foreach (static::filters() as $attribute => $filter) {
            if (isset($filters[$attribute])) {
                $valid[$attribute] = $filters[$attribute];
            }
        }

        return $valid;
    }

    /**
     * Return the model's filter handlers in key => closure form.
     *
     * Eg. return [
     *     'name' => function ($query, $value, $attribute) {
     *         return $query->where('name', 'LIKE', $value . '%');
     *     }
     * ];
     *
     * @return array
     */
    public static function filters()
    {
        return [];
    }
}
