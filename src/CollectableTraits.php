<?php
namespace PsgcLaravelPackages\Collector;

trait CollectableTraits
{
    protected static $_collector = null;

    // collector factory
    public static function collector()
    {
        if ( is_null( self::$_collector ) ) {
            self::$_collector = new Collector( self::query() ); // Eloquent implemenation
        }

        // Assign/Implement delegates for the collector...
        self::$_collector->_filterQueryDelegate = self::class.'::filterQuery';
        self::$_collector->_searchQueryDelegate = self::class.'::searchQuery';
        self::$_collector->_sortQueryDelegate = self::class.'::sortQuery';

        return self::$_collector;
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
