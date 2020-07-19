<?php

namespace App\Http\Controllers;

use App\Models\Measurement;
use App\Models\MeasurementType;
use App\Services\HealthTrackService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppController extends Controller
{
    function index()
    {
        $types = [
            ['sys', 'dia'],
            ['weight'],
            ['hamster']
        ];
        $i = 0;
        $forms = [];
        foreach ($types as $type) {
            $i++;
            $forms[] = [
                'formId' => "form{$i}",
                'target' => "graph{$i}",
                'types' => $type,
                'fields' => MeasurementType::whereIn('code', $type)->get()
            ];
        }

        return view('index', ['forms' => $forms]);
    }

    function save(Request $request)
    {
        $vars = $this->getSaveVars($request);
        $ts = new Carbon();
        foreach ($vars as $typeId => $value) {
            Measurement::create([
                'type_id' => $typeId,
                'value' => $value,
                'ts' => $ts
            ]);
        }
        return 'success';
    }

    function getSaveVars(Request $request)
    {
        $vars = $request->post();
        if (array_key_exists('_token', $vars)) {
            unset($vars['_token']);
        }
        $saveVars = [];
        foreach ($vars as $key => $var) {
            if (strpos('type_id_', $key) == 0) {
                $rowId = str_replace('type_id_', '', $key);
                if (array_key_exists('field_' . $rowId, $vars)) {
                    $saveVars[$var] = $this->reformatValue($var, $vars['field_' . $rowId]);
                }
            }
        }

        return $saveVars;
    }

    function reformatValue($typeId, $value)
    {
        $type = MeasurementType::find($typeId);
        $value = round($value * pow(10, ($type->decimals)));
        return (int)$value;
    }

    function chart($types)
    {

        $types = explode(',', $types);
        /** @var HealthTrackService $healthTrackService */
        $healthTrackService = app()->make(HealthTrackService::class);
        $measures = $healthTrackService->getChartData($types);
        return json_encode($measures);
        // print "<pre>" . print_r($measures, true) . "</pre>";

    }
}
