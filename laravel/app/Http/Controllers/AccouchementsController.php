<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Accouchements;

class AccouchementsController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accouchements = DB::table('accouchements')
        
        ->leftJoin('provinces', function($join){
            $join->on('accouchements.province_id', '=', 'provinces.id');
        })
        ->leftJoin('communes', function($join){
            $join->on('accouchements.commune_id', '=', 'communes.id');
        })
        ->leftJoin('regions', function($join){
            $join->on('accouchements.region_id', '=', 'regions.id');
        })
        ->leftJoin('users', function($join){
            $join->on('accouchements.created_by', '=', 'users.id');
        })
        ->leftJoin('districts', function($join){
            $join->on('accouchements.district_id', '=', 'districts.id');
        })
        ->leftJoin('formation_sanitaires', function($join){
            $join->on('accouchements.formation_sanitaire_id', '=', 'formation_sanitaires.id');
        })
        ->select('accouchements.*', 'users.name as author', 
        'districts.nom_district as district', 
        'regions.region as region', 
        'provinces.province as province',
        'communes.commune as commune',
        'formation_sanitaires.nom_structure as formation_sanitaire')
        ->get();
        return response()->json( $accouchements );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $regions = DB::table('regions')->select('regions.region as label', 'regions.id as value')->get();
        $provinces = DB::table('provinces')->select('provinces.province as label', 'provinces.id as value')->get();
        $communes = DB::table('communes')->select('communes.commune as label', 'communes.id as value')->get();
        $districts = DB::table('districts')->select('districts.nom_district as label', 'districts.id as value')->get();
        $formationSanitaires = DB::table('formation_sanitaires')->select('formation_sanitaires.nom_structure as label', 'formation_sanitaires.id as value')->get();
        return response()->json( ['communes'=>$communes,'regions'=>$regions,'provinces'=>$provinces,'districts'=>$districts,'formationSanitaires'=>$formationSanitaires] );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'region_id' => 'required',
            'province_id' => 'required',
            'commune_id' => 'required',
            'district_id' => 'required',
            'formation_sanitaire_id' => 'required'
        ]);
        $user = auth()->userOrFail();
        $accouchements = new Accouchements();

        $accouchements->region_id = $request->input('region_id');
        $accouchements->province_id = $request->input('province_id');
        $accouchements->commune_id = $request->input('commune_id');
        $accouchements->district_id = $request->input('district_id');
        $accouchements->formation_sanitaire_id = $request->input('formation_sanitaire_id');
        $accouchements->annee = $request->input('annee');
        $accouchements->mois = $request->input('mois');
        
        $accouchements->NbAccouchement_Normaux = $request->input('NbAccouchement_Normaux');
        $accouchements->NbAccouchement_Assiste = $request->input('NbAccouchement_Assiste');
        $accouchements->NbAccouchement_Cesarienne = $request->input('NbAccouchement_Cesarienne');
        $accouchements->NbAccouchement_Partogramme = $request->input('NbAccouchement_Partogramme');
        $accouchements->NNaissance_vivante = $request->input('NNaissance_vivante');
        $accouchements->NbMortNe_frais = $request->input('NbMortNe_frais');
        $accouchements->NbMortNe_Macere = $request->input('NbMortNe_Macere');

        $accouchements->created_by = $user->id;
        $accouchements->save();
        return response()->json( ['status' => 'success'] );
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
        ]);
        $user = auth()->userOrFail();

        foreach($request->input('items')  as $item ){
            $accouchements = new Accouchements();

            $accouchements->region_id = $item['region'];
            $accouchements->province_id = $item['province'];
            $accouchements->commune_id = $item['commune'];
            $accouchements->district_id = $item['district'];
            $accouchements->formation_sanitaire_id = $item['formation_sanitaire'];
            $accouchements->annee = $item['annee'];
            $accouchements->mois = $item['mois'];

            $accouchements->NbAccouchement_Normaux = $item['NbAccouchement_Normaux'];
            $accouchements->NbAccouchement_Assiste = $item['NbAccouchement_Assiste'];
            $accouchements->NbAccouchement_Cesarienne = $item['NbAccouchement_Cesarienne'];
            $accouchements->NbAccouchement_Partogramme = $item['NbAccouchement_Partogramme'];
            $accouchements->NNaissance_vivante = $item['NNaissance_vivante'];
            $accouchements->NbMortNe_frais = $item['NbMortNe_frais'];
            $accouchements->NbMortNe_Macere = $item['NbMortNe_Macere'];

            $accouchements->created_by = $user->id;
            $accouchements->save();
        }
        return response()->json( ['status' => 'success'] );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $district = DB::table('accouchements')
        
        ->leftJoin('provinces', function($join){
            $join->on('accouchements.province_id', '=', 'provinces.id');
        })
        ->leftJoin('communes', function($join){
            $join->on('accouchements.commune_id', '=', 'communes.id');
        })
        ->leftJoin('regions', function($join){
            $join->on('accouchements.region_id', '=', 'regions.id');
        })
        ->leftJoin('users', function($join){
            $join->on('accouchements.created_by', '=', 'users.id');
        })
        ->leftJoin('users as users2', function($join){
            $join->on('accouchements.updated_by', '=', 'users2.id');
        })
        ->leftJoin('districts', function($join){
            $join->on('accouchements.district_id', '=', 'districts.id');
        })
        ->leftJoin('formation_sanitaires', function($join){
            $join->on('accouchements.formation_sanitaire_id', '=', 'formation_sanitaires.id');
        })
        ->select('accouchements.*', 'users.name as created_by','users2.name as updated_by', 'regions.region as region', 'districts.nom_district as district',
        'provinces.province as province','communes.commune as commune','formation_sanitaires.nom_structure as formationSanitaire')
        ->where('accouchements.id', '=', $id)
        ->first();
        return response()->json( $district );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $accouchement = DB::table('accouchements')->where('id', '=', $id)->first();
        $regions = DB::table('regions')->select('regions.region as label', 'regions.id as value')->get();
        $provinces = DB::table('provinces')->select('provinces.province as label', 'provinces.id as value')->get();
        $communes = DB::table('communes')->select('communes.commune as label', 'communes.id as value')->get();
        $districts = DB::table('districts')->select('districts.nom_district as label', 'districts.id as value')->get();
        $formationSanitaires = DB::table('formation_sanitaires')->select('formation_sanitaires.nom_structure as label', 'formation_sanitaires.id as value')->get();

        return response()->json( [ 'provinces' => $provinces, 'regions' => $regions, 'districts' => $districts, 'communes' => $communes,'accouchement'=>$accouchement,'formationSanitaires'=>$formationSanitaires ] );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'region_id' => 'required',
            'province_id' => 'required',
            'commune_id' => 'required',
            'district_id' => 'required',
            'formation_sanitaire_id' => 'required'
        ]);

        $user = auth()->userOrFail();
        $accouchements = Accouchements::find($id);

        $accouchements->region_id = $request->input('region_id');
        $accouchements->province_id = $request->input('province_id');
        $accouchements->commune_id = $request->input('commune_id');
        $accouchements->district_id = $request->input('district_id');
        $accouchements->formation_sanitaire_id = $request->input('formation_sanitaire_id');
        $accouchements->annee = $request->input('annee');
        $accouchements->mois = $request->input('mois');
        
        $accouchements->NbAccouchement_Normaux = $request->input('NbAccouchement_Normaux');
        $accouchements->NbAccouchement_Assiste = $request->input('NbAccouchement_Assiste');
        $accouchements->NbAccouchement_Cesarienne = $request->input('NbAccouchement_Cesarienne');
        $accouchements->NbAccouchement_Partogramme = $request->input('NbAccouchement_Partogramme');
        $accouchements->NNaissance_vivante = $request->input('NNaissance_vivante');
        $accouchements->NbMortNe_frais = $request->input('NbMortNe_frais');
        $accouchements->NbMortNe_Macere = $request->input('NbMortNe_Macere');
        $accouchements->updated = 1;
        
        $accouchements->updated_by = $user->id;
        $accouchements->save();
        return response()->json( ['status' => 'success'] );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $accouchements = Accouchements::find($id);
        if($accouchements){
            $accouchements->delete();
        }
        return response()->json( ['status' => 'success'] );
    }
}
