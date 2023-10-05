<?php

namespace App\Filters;

use Illuminate\Http\Request;

class HairSalonFilter
{
    protected $params = [
        'cityId' => ['eq'],
    ];



    protected $columnMap = [
        'cityId' => 'city_id'
    ];

    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>='
    ];

    public function transform(Request $request)
    {
        $eloQuery = [];
        foreach($this->params as $param => $operators)
        {
            $query = $request->query($param);
            if(!$query) continue;
            $column = $this->columnMap[$param] ?? $param;
            foreach($operators as $operator)
            {
                if($query[$operator])
                {
                    $eloQuery[] = [$column, $this->operatorMap[$operator], $query[$operator]];
                }
            }
        }
        return $eloQuery;
    }
}


?>