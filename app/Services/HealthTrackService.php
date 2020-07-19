<?php

namespace App\Services;

use App\Models\Measurement;
use App\Models\MeasurementType;
use Carbon\Carbon;

class HealthTrackService
{
    public function getChartData(array $typeParams)
    {
        $typesModel = MeasurementType::whereIn('code', $typeParams);


        $data = [];
        foreach ($typesModel->get() as $type) {
            $data[$type->code] = [];
            $plots = [];
            foreach (
                Measurement::where('type_id', $type->id)
                    ->select(['value', 'ts'])
                    ->orderBy('ts')
                    ->get()
                as $measure
            ) {
                $date = (new Carbon($measure->ts))->toDateString();
                if (!array_key_exists($date, $plots)) {
                    $plots[$date] = [];
                }
                $plots[$date][] = $measure->value;
            }
            $data[$type->code] = $this->compilePlots($type, $plots);
        }


        return $this->makeGraphObject($data);
    }

    private function compilePlots(MeasurementType $type, array $plots)
    {
        foreach ($plots as $key => $plot) {
            $plots[$key] = $this->convertDecimal($this->{$type->aggr}($plot), $type->decimals);
        }
        return $plots;
    }


    private function sum(array $points)
    {
        return round(array_sum($points));
    }

    private function avg(array $points)
    {
        return array_sum($points) / count($points);
    }

    private function convertDecimal($value, $decimals)
    {
        return round($value / pow(10, ($decimals)), $decimals);
    }


    private function makeGraphObject($data)
    {
        list($labels, $datasets) = $this->collectGraphVars($data);
        return [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => $datasets
            ]
        ];
    }

    private function collectGraphVars($data)
    {
        $datasets = [];
        $labels = [];
        foreach ($data as $key => $value) {
            $labels = array_keys($value);
            $datasets[] = [
                'label' => $key,
                'data' => array_values($value),
                'fill' => false,
                'borderColor' => '#333',
                'borderWidth' => 4,
            ];

        }
        return [$labels, $datasets];
    }

    /**
     * {
     * type: 'line', // bar hoizontalBar pie line doughnut, radar, polarArea
     * data: {
     * labels: [
     * '2020-07-18', '2020-07-19'
     * ],
     * datasets: [
     * {
     * label: 'sys',
     * data: [133, 141],
     * borderColor: 'red',
     * borderWidth: 4,
     * fill: false,
     *
     * },
     * {
     * label: 'dias',
     * data: [75, 91],
     * borderColor: 'blue',
     * borderWidth: 4,
     * fill: false,
     *
     * }
     * ],
     *
     * },
     * }
     */
}
