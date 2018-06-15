<?php
namespace PsgcLaravelPackages\Collector;

trait CollectableTraits
{

    // collector factory
    public static function collector()
    {
        $_collector = new Collector( self::query() ); // Eloquent implemenation

        // Assign/Implement delegates for the collector...
        $_collector->_filterQueryDelegate = self::class.'::filterQuery';
        $_collector->_searchQueryDelegate = self::class.'::searchQuery';
        $_collector->_sortQueryDelegate = self::class.'::sortQuery';

        return $_collector;
    }

    // %TODO: default implemenations (except for filter)

    // queryApplySort
    public static function sortQuery(&$query,$sorting)
    {
        if ( !empty($sorting['value']) ) {
            $direction = !empty($sorting['direction']) ? $sorting['direction'] : 'asc';
            $query->orderBy($sorting['value'], $direction);
        }
        return $query;
    }

}
