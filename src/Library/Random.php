<?php

namespace Inium\Laraboard\Library;

class Random
{
    /**
     * Get the single probability check with linear probabilit
     *
     * @param float $probability        Probaility
     * @param integer $length           Length
     * @return boolean
     * @see https://stackoverflow.com/questions/21572363/generate-random-numbers-with-fix-probability
     */
    public function probability(float $probability = 0.1,
                                       int $length = 1000): bool
    {
        $test = mt_rand(1, $length);
        return $test <= $probability * $length;
    }

    /**
     * Weighted Random (가중치 랜덤)
     *
     * @param array $weightedValues 가중치 정보. 아래와 같이 입력.
     *                              array('A' => 10, 'B' => 40, 'C' => 50)
     * @return string|bool          랜덤확정된 배열 key 혹은 계산실패(false)
     * @see https://gist.github.com/irazasyed/f41f8688a2b3b8f7b6df
     */
    public function weighted(array $weightedValues)
    {
        $rand = mt_rand(1, (int)array_sum($weightedValues));

        foreach ($weightedValues as $key => $value) {
            $rand -= $value;
            if ($rand <= 0) {
                return $key;
            }
        }

        return false;
    }
}
