<?php

namespace Inium\Laraboard\Support\Collection;

use Illuminate\Support\Arr;

class OnlyOrAll
{
    public function __invoke()
    {
        return function ($attributes) {
            return $this->map(function ($item) use ($attributes) {

                if (is_null($attributes)) {
                    return $item;
                }
                else {
                    $arr = Arr::dot($item->toArray());
                    return Arr::only($arr, $attributes);

                    // foreach ($arrayDot as $key => $value) {
                    //     array_set($array, $key, $value);
                    //   }

                    // return Arr::set($ret);
                }

                // return $item->only($attributes);

            });
        };
    }
}
