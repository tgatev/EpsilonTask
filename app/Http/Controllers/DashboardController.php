<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OAuth2Wrapper\EpsilonApiClient;
use App\Facades\EpsilonApi;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    //

    public function index(){
        return view("pages.home");
    }

    /**
     * Datatables view
     *
     */
    public function displayServices(){
        return view('pages.display_services');
    }

    /**
     * Build data for datatables, Services listing
     * @return mixed
     */
    public function getServicesData(){
        $data = (array)EpsilonApi::request("GET" ,"services");
        return DataTables::of($data["services"])
            ->editColumn('id', function($row) {
                if(isset($row->id)) {
                    return view('datatables.service_link_id', [ 'id' => $row->id ])->render();
                }else{
                    return "";
                }
            })
            ->editColumn('port', function($row) {
                if(isset($row->b_port)) {
                    return view('datatables.bport', [ 'Data' => $row->port ])->render();
                }else{
                    return "";
                }
            })
            ->editColumn('b_port', function($row) {
                if(isset($row->b_port)) {
                    return view('datatables.bport', [ 'Data' => $row->b_port ])->render();
                }else{
                    return "";
                }
            })
            ->make(true);
    }

    /**
     * Display Services details by id
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function displayServiceDetails($id){

        $data = EpsilonApi::request("GET" ,sprintf("%s/%d/%s" , "services", $id, "service"));

        $ServiceDetails = (object) [
            "name" => static::propertyPathExists($data, "name")? $data->name: null,
            "port_name" => static::propertyPathExists($data, "port->name")? $data->port->name: null,
            "b_port_name" => static::propertyPathExists($data, "b_port->name")? $data->b_port->name: null,
            "status_state" => static::propertyPathExists($data, "csp_status->state")? $data->csp_status->state: null,
            "two_way_latency" => static::propertyPathExists($data, "statistics->2_way_latency->lastvalue")? $data->statistics->{"2_way_latency"}->lastvalue: null,
            "discard" => static::propertyPathExists($data, "statistics->discards->lastvalue")? $data->statistics->discards->lastvalue: null,
        ];

        return view('pages.service_details' , ["ServiceDetails" => $ServiceDetails]);
    }

    /**
     * Check for searched property
     * @param $object
     * @param $property_path
     * @return bool
     */
    public static function propertyPathExists($object, $property_path)
    {
        $path_components = explode('->', $property_path);

        if (count($path_components) == 1) {
            return isset($object->{$property_path});
        } else {
            return (
                isset($object->{$path_components[0]}) &&
                static::propertyPathExists(
                    $object->{array_shift($path_components)},
                    implode('->', $path_components)
                )
            );
        }
    }
}
