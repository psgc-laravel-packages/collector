<?php 
namespace PsgcLaravelPackages\Collector;

final class Collector 
{
    protected $_query = null;

    public $_filterQueryDelegate = null;
    public $_searchQueryDelegate = null;
    public $_pageQueryDelegate = null;

    public function __construct($query)
    {
        $this->_query = $query;
    }

    public function getCount($filters=[],$search=[])
    {
        // no sort or paging
        $query = $this->_query;
        $query = call_user_func_array( $this->_filterQueryDelegate,[&$query,$filters] ); // apply filters
        $query = call_user_func_array( $this->_searchQueryDelegate,[&$query,$search] ); // apply search
        $this->_count = $query->count();
        return $this->_count;
    }

    public function getList($filters=[],$search=[],$paging=[],$sorting=[],$withs=[])
    {
        $query = $this->_query; // %FIXME: make sure this is copy by value!
        foreach ($withs as $w) {
            $query = $query->with($w); // works
        }
        $query = call_user_func_array( $this->_filterQueryDelegate,[&$query,$filters] ); // apply filters
        $query = call_user_func_array( $this->_searchQueryDelegate,[&$query,$search] ); // apply search
        $query = call_user_func_array( $this->_sortQueryDelegate,[&$query,$sorting] ); // apply sort
        $query = self::applyPaging($query,$paging);
        return $query->get();
    } // getList()

    // Paging is same for all models
    //   $paging = [
    //              'length' => {number of items per page},
    //              'offset' => {start on item #} | 'page' => {start on page #},
    //             ]
    protected static final function applyPaging(&$query,$paging)
    {
        if ( !empty($paging) && array_key_exists('length',$paging) ) {

            $take = intval($paging['length']);

            $skip = array_key_exists('offset',$paging) 
                    ? intval($paging['offset'])         // start offset is from 0
                    : (array_key_exists('page',$paging) // start page is from 1
                        ? $take * (intval($paging['page'])-1)
                        : 0); // default to skip 0

            $take = max(0,$take); // floor to 0

            //dd($skip,$take);
            $query->skip($skip);
            $query->take($take);
        }
        return $query;
    }
}
