<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Accouchements;

class ZReceiveBKController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    

    /**
     * storeMany a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeMany(Request $request)
    {
        $validatedData = $request->validate([
            'items' => 'required',
            'nom_table' => 'required'
        ]);
        $items = json_decode($request->input('items'),true);
        // Si nous trouvons l'élément dans la base de données, nous procedons à une modification
        // Le cas contraire, nous l'ajoutons
        foreach($items  as $item ){
            $data = DB::table($request->input('nom_table'))->where('id', '=', $item['id'])->first();
            if($data!==null)
            DB::table($request->input('nom_table'))->where('id','=',$item['id'])->update($item);
            else
            DB::table($request->input('nom_table'))->insertGetId($item);
        }

        return response()->json( ['status' => 'success'] );
    }
}
