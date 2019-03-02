<?php
namespace PsgcLaravelPackages\Collector;

// ref: 
//   ~ https://stackoverflow.com/questions/46795357/dynamic-eloquent-query-injecting-is-not-working-in-laravel
//   ~ https://github.com/psgc-laravel-packages/collector/commit/f0f675b96ab84525f9475f64b1cc8b4a8e19a64b#diff-a86dec1b33fef51cff5407a0f4112b06
//   ~ => https://stackoverflow.com/questions/27445232/change-connection-on-builder-get
//   ~ https://medium.com/teknomuslim/how-to-run-laravel-using-multiple-database-connections-d7e8dcb7ed2a
//   ~ https://laravel.com/api/5.5/Illuminate/Database/Schema/Builder.html
//   ~ https://stackoverflow.com/questions/41216195/best-way-to-make-a-connection-to-database-in-laravel
//   ~ 
//   ~ 
//   ~ 
//   ~ 
/*
    // Retrieve all french words by user (via model)
    $model = new Word;
    $model->connection = 'french'; // set connection on-the-fly
    $model->where('user_id',5) // !! doesn't work, not added to query
    // ...
    $words = $model->get();

    // Retrieve all french words by user (via query)
    $query = Word::query(); 
    $query->connection = 'french'; // !! doesn't work, how to set set connection ? <= want to do this
    $query->where('user_id',5) // dyanmically adding where works in this case
    // ...
    $words = $query->get();
 */
trait CollectableTraits
{
    // collector factory
    public static function collector(string $dbConnection=null) : Collector
    {
        /*
        $m = new self; // Eloquent model object
        if ( !empty($dbConnection) ) {
            $m->connection = $dbConnection;
        } // ...otherwise will just use default
        $_collector = new Collector( $m ); // Eloquent implemenation
         */

        //dd(self::query());
        $_collector = new Collector( self::on($dbConnection) ); 

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
