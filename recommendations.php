<?php
class recommendations
{
    function sim_pearson($prefs, $p1, $p2){
        $si = array();
        foreach (array_keys($prefs[$p1]) as $item) {
            if (isset($prefs[$p2][$item])) {
                $si[$item] = 1;
            }
        }
        $n = count($si);
        if ($n == 0) {
            return 0;
        }
        $sum1 = $sum2 = $sum1Sq = $sum2Sq = $pSum = 0.0;
        foreach (array_keys($si) as $it) {
            $sum1 += $prefs[$p1][$it];
            $sum2 += $prefs[$p2][$it];
            $sum1Sq += pow($prefs[$p1][$it], 2);
            $sum2Sq += pow($prefs[$p2][$it], 2);
            $pSum += $prefs[$p1][$it] * $prefs[$p2][$it];
        }
        $num = $pSum - ($sum1 * $sum2 / $n);
        $den = sqrt(($sum1Sq - pow($sum1, 2) / $n) * ($sum2Sq - pow($sum2, 2) / $n));
        if ($den == 0) {
            return 0;
        }
        return $num / $den;
    }

    public function topMatches($prefs, $person, $n = 5, $similarity='sim_pearson')
    {
        $scores = array();
        foreach ($prefs as $key => $val) {
            if ($key == $person) {
                continue;
            }
            $scores[$key] = $this->$similarity($prefs, $person, $key);
        }
        arsort($scores);

        return array_slice($scores, 0, $n, true);
    }

    public function getRecommendations($prefs, $person, $similarity='sim_pearson')
    {
        $totals = array();
        $simSums = array();
        foreach (array_keys($prefs) as $other) {
            if ($person == $other) {
                continue;
            }
            $sim = $this->$similarity($prefs, $person, $other);
            if ($sim <= 0) {
                continue;
            }
            foreach (array_keys($prefs[$other]) as $item) {
                if (0 != $prefs[$person][$item]) {
                    continue;
                }
                $totals[$item] += $prefs[$other][$item] * $sim;
                $simSum[$item] += $sim;
            }
        }
        $rankings = array();
        foreach (array_keys($totals) as $item) {
            $rankings[$item] = $totals[$item] / $simSum[$item];
        }

        arsort($rankings);
        return $rankings;
    }
}

$critics = array(
    'Lisa Rose'=> array(
        'Lady in the Water'=> 2.5,
        'Snakes on a Plane'=> 3.5,
        'Just My Luck'=> 3.0,
        'Superman Returns'=> 3.5,
        'You, Me and Dupree'=> 2.5,
        'The Night Listener'=> 3.0),
    'Gene Seymour'=> array(
        'Lady in the Water'=> 3.0,
        'Snakes on a Plane'=> 3.5,
        'Just My Luck'=> 1.5,
        'Superman Returns'=> 5.0,
        'The Night Listener'=> 3.0,
        'You, Me and Dupree'=> 3.5),
    'Michael Phillips'=> array(
        'Lady in the Water'=> 2.5,
        'Snakes on a Plane'=> 3.0,
        'Superman Returns'=> 3.5,
        'The Night Listener'=> 4.0),
    'Claudia Puig'=> array(
        'Snakes on a Plane'=> 3.5,
        'Just My Luck'=> 3.0,
        'The Night Listener'=> 4.5,
        'Superman Returns'=> 4.0,
        'You, Me and Dupree'=> 2.5),
    'Mick LaSalle'=> array(
        'Lady in the Water'=> 3.0,
        'Snakes on a Plane'=> 4.0,
        'Just My Luck'=> 2.0,
        'Superman Returns'=> 3.0,
        'The Night Listener'=> 3.0,
        'You, Me and Dupree'=> 2.0),
    'Jack Matthews'=> array(
        'Lady in the Water'=> 3.0,
        'Snakes on a Plane'=> 4.0,
        'The Night Listener'=> 3.0,
        'Superman Returns'=> 5.0,
        'You, Me and Dupree'=> 3.5),
    'Toby'=> array(
        'Snakes on a Plane'=>4.5,
        'You, Me and Dupree'=>1.0,
        'Superman Returns'=>4.0)
    );

$recommend = new recommendations();
$res = $recommend->sim_pearson($critics, 'Lisa Rose', 'Gene Seymour');
var_dump($res);
$res = $recommend->topMatches($critics, 'Toby', 3);
var_dump($res);
$res = $recommend->getRecommendations($critics, 'Toby');
var_dump($res);
?>