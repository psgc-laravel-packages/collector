<?php 
namespace PsgcLaravelPackages\Collector;

use Illuminate\Database\Eloquent\Model;

final class Collector 
{
    protected $_model = null;

    public $_filterQueryDelegate = null;
    public $_searchQueryDelegate = null;
    public $_pageQueryDelegate = null;

    public function __construct(Model $model)
    {
        $this->_model = $model;
    }

    public function getCount($filters=[],$search=[])
    {
        // no sort or paging
        $model = $this->_model;
        $model = call_user_func_array( $this->_filterQueryDelegate,[&$model,$filters] ); // apply filters
        $model = call_user_func_array( $this->_searchQueryDelegate,[&$model,$search] ); // apply search
        $this->_count = $model->count();
        return $this->_count;
    }

    public function getList($filters=[],$search=[],$paging=[],$sorting=[],$withs=[])
    {
        $model = $this->_model; // %FIXME: make sure this is copy by value!
        foreach ($withs as $w) {
            $model = $model->with($w); // works
        }
        $model = call_user_func_array( $this->_filterQueryDelegate,[&$model,$filters] ); // apply filters
        $model = call_user_func_array( $this->_searchQueryDelegate,[&$model,$search] ); // apply search
        $model = call_user_func_array( $this->_sortQueryDelegate,[&$model,$sorting] ); // apply sort
        $model = self::applyPaging($model,$paging);
        return $model->get();
    } // getList()

    // Paging is same for all models
    //   $paging = [
    //              'length' => {number of items per page},
    //              'offset' => {start on item #} | 'page' => {start on page #},
    //             ]
    protected static final function applyPaging(&$model,$paging)
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
            $model->skip($skip);
            $model->take($take);
        }
        return $model;
    }
}
