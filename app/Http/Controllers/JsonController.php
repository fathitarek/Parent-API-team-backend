<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class JsonController extends Controller {

    public function index() {

        $path = "uploads/data.json";
        $json = file_get_contents($path);
        $arr = json_decode($json, TRUE);

        $collection = new Collection($arr['x']);
        if (isset($_GET['currency'])) {
            $collection = $collection->where('currency', $_GET['currency']);
            $filtered = $collection->all();
        }

        if (isset($_GET['balanceMin']) && isset($_GET['balanceMax'])) {
            $collection = $collection->whereBetween('balance', [$_GET['balanceMin'], $_GET['balanceMax']]);
            $filtered = $collection->all();
        }


        if (isset($_GET['provider']) && $_GET['provider'] == 'DataProviderX') {
            $collection = $collection->whereNotNull('parentEmail');
            $filtered = $collection->all();
        }

        if (isset($_GET['provider']) && $_GET['provider'] == 'DataProviderY') {
            $collection = $collection->whereNull('parentEmail');
            $filtered = $collection->all();
        }
        if (isset($_GET['statusCode'])) {
            if ($_GET['statusCode'] == 'authorised') {
                $collection = $collection->where('statusCode', 1);
                $filtered = $collection->all();
            }
            if ($_GET['statusCode'] == 'decline') {
                $collection = $collection->where('statusCode', 2);
                $filtered = $collection->all();
            }
            if ($_GET['statusCode'] == 'refunded') {
                $collection = $collection->where('statusCode', 3);
                $filtered = $collection->all();
            }
        }
        if (isset($filtered)) {
//            $filtered->all();
            return $filtered;
        } else {
            return response()->json(['message' => 'Data MisMatching (Not Found)', 'success' => 'false', 'status' => 404]);
        }
    }

}
