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


        return $this->makeGraphObject($data, $this->getAnnotations($typeParams));
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


    private function makeGraphObject($data, $annotations = [])
    {
        list($labels, $datasets) = $this->collectGraphVars($data);

        return [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => $datasets
            ],
            'options' => [
                'scales' => [
                    'yAxis' => [
                        [
                            'ticks' => [
                                'beginAtZero' => true
                            ]
                        ]
                    ]

                ],
                'annotation' => [
                    'annotations' => $annotations
                ]
            ]
        ];
    }

    /**
     * @param $typeParamsoptions : { //your chart options
     * annotation: {
     * annotations: [{
     * type: 'box',
     * drawTime: 'beforeDatasetsDraw',
     * yScaleID: 'y-axis-0',
     * yMin: 40,
     * yMax: 50,
     * backgroundColor: 'rgba(0, 255, 0, 0.1)'
     * }]
     * }
     * }
     */

    /**
     * @param array $typeParams
     * @return array
     */
    private function getAnnotations(array $typeParams)
    {
        $annotations = [];
        if (in_array('sys', $typeParams)) {
            $annotations[] =
                [
                    'type' => 'box',
                    'drawTime' => 'beforeDatasetsDraw',
                    'yScaleID' => 'y-axis-0',
                    'yMin' => 110,
                    'yMax' => 135,
                    'backgroundColor' => 'rgba(0, 255, 0, 0.1)'
                ];
        }
        if (in_array('dia', $typeParams)) {
            $annotations[] =
                [
                    'type' => 'box',
                    'drawTime' => 'beforeDatasetsDraw',
                    'yScaleID' => 'y-axis-0',
                    'yMin' => 85,
                    'yMax' => 65,
                    'backgroundColor' => 'rgba(0, 255, 0, 0.1)'
                ];
        }
        return $annotations;
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
