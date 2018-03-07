# Collector Library

A simple but effective package for getting a list of records with filtering, sorting, paging, and search. Impelementation uses Eloquent (as opposed to Query Builder). The targeted use case is rapid prototypeing of an MVP.

The library's design is inspired by the use of "Delegates" in iOS/Swift programming. This is my first attempt at a PHP implementation, I'm sure it can be improved upon ;). Basically the idea is to encaspluate in the package the common functionality invovled in getting list data. The parts that are specific to the model itself (the type of data being fetched) are 'delegated' to the actual model classes.  For intance, getting a list of all "widget" can probably be done by generic code, however only a "Widget" knows how to filter a list of itself. This filter functionality is "delegated" to the Widget model class.

## Install

Add to project's composer.json...

    "repositories": [
        ...
        {
            "type": "vcs",
            "url":  "git@github.com:psgc-laravel-packages/collector.git"
        },
        ...
    ],

    "require": {
        ...
        "psgc-laravel-packages/collector": "dev-master",
        ...
    },

Run composer update

## Usage

Let's say you have a DB table called 'widgets' with an associated model 'Widget.php'. 

The table widgets has columns `name`, and `guid`, and `owner_id`, which is a foreign key to a table of users.

### Setup model

```php
namespace App\Models;

// (1) Add these
use PsgcLaravelPackages\Collector\Collectable;
use PsgcLaravelPackages\Collector\CollectableTraits;

// (2) use the interface 'Collectable'
class Widget implements Collectable
{
    // (3) Use CollectableTraits (default 'core' implementation)
    use CollectableTraits;

    ...

    // (4) Implement interface method that tells the collector how it should 'filter' widgets...
    //        NOTE: not part of default implemenation
    public static function filterQuery(&$query,$filters)
    {
        if ( !empty($filters['owner_id']) ) {
            $query->where('owner_id',$filters['owner_id']);
        }
        return $query;
    }


    // (5) Implement interface method that tells the collector how it should 'search' widgets...
    //        NOTE: not part of default implemenation
    public static function searchQuery(&$query,$search)
    {
        if ( empty($search) || ( is_array($search) && !array_key_exists('value',$search) ) ) {
            return $query; // no search string, ignore
        }
        $searchStr = is_array($search) ? $search['value'] : $search; // latter is simple string
        $query->where( function ($q1) use($searchStr) {
            $q1->where('guid', 'like', '%'.$searchStr.'%');
            $q1->orWhere('name', 'like', $searchStr.'%');
        });
        return $query;

    } // applySearch()

}
```

### Use in a controller

```php
use App\Models\Widget

class MyController {

    ...
    //$paging = ['page'=>2,'length'=>10];
    $paging = ['offset'=>0,'length'=>10];
    $sorting = ['value'=>'id','direction'=>'asc'];
    $search = ['foo'];
    $filters = ['owner_id'=>3];

//$search = 'vel';
    $recordsTotal    = Widget::collector()->getCount();
    $recordsFiltered = Widget::collector()->getCount($filters,$search);
    $records         = Widget::collector()->getList($filters,$search,$paging,$sorting);
}
```
