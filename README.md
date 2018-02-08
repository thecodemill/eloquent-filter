# EloquentFilter

Add simple filterability to Eloquent models.

## Installation

Install the EloquentFilter package via Composer:

```
composer require thecodemill/eloquent-filter
```

## Usage

As the name suggests, this package is intended for use with [Eloquent](https://github.com/illuminate/database) and therefor works seamlessly with Laravel.

EloquentFilter allows you to define filter handlers on any of your app's models to make querying much simpler. A filter handler is very similar to a local scope, but by using the `Model::filter()` method, any number of scopes may be applied at once without the need for chaining the individual scopes or query modifiers.

Eg.

```php
// With query modifiers
$users = User::where('email', $request->input('email'))
    ->where('age', $request->input('age'))
    ->whereHas('subscription', function ($query) use ($request) {
        $query->where('code', $request->input('subscription'));
    })
    ->get();

// Using EloquentFilter's filter handlers
$users = User::filter($request->all())->get();
```

This is made possible by adding a `filters()` method to the model itself. This method should return a key => value array of filter names and their respective closures.

```php
namespace App;

use TheCodeMill\EloquentFilter\Filterable;

class User
{
    use Filterable;
    
    /**
     * Return the filter handlers.
     *
     * @return array
     */
    public static function filters()
    {
        return [
            'email' => function ($query, $value) {
                return $query->where('email', $value);
            },
            'age' => function ($query, $value) {
                return $query->where('age', $value);
            },
            'subscription' => function ($query, $value) {
                return $query->whereHas('subscription', function ($query) use ($value) {
                    $query->where('code', $value);
                });
            }
        ];
    }
}
```

If using pagination, EloquentFilter allows you to maintain the query parameters in between page requests. To do so, render the pagination links in your Blade template with the addition of the valid filters that are present in the original page request.

```php
// Paginate models in your controller/repository/etc.
$users = User::filter($request->all())->paginate();

// In Blade, append only the valid query parameters:
{{ $users->appends(App\User::validFilters(request()->all()))->links() }}
```

## Author

* [Andrew Robinson](https://twitter.com/ap_robinson)
