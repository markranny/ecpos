<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\inventtables;
use App\Models\inventjournaltables;
use App\Models\inventjournaltrans;
use App\Models\inventtablemodules;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class PickListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /* public function index()
    {
        $currentDateTime = Carbon::now()->toDateString();
        $storeId = Auth::user()->storeid;
        $role = Auth::user()->role;

        $picklist = DB::table('inventjournaltables AS b')
            ->select('b.journalid', 'f.STOREID', 'b.POSTEDDATETIME', 'c.ADJUSTMENT', 'b.STOREID AS STORENAME',
                    'd.itemid AS ITEMID', 'd.itemname AS ITEMNAME', 'e.ITEMGROUP AS CATEGORY', 'c.COUNTED AS COUNTED')
            ->leftJoin('inventjournaltrans AS c', 'b.JOURNALID', '=', 'c.JOURNALID')
            ->leftJoin('inventtables AS d', 'c.ITEMID', '=', 'd.itemid')
            ->leftJoin('rboinventtables AS e', 'd.ITEMID', '=', 'e.itemid')
            ->leftJoin('rbostoretables AS f', 'b.STOREID', '=', 'f.NAME')
            ->whereRaw("DATE(b.createddatetime) = ?", [$currentDateTime])
            ->where('b.POSTED', '=', '1')
            ->where('c.counted', '!=', '0')
            ->orderByRaw('CAST(f.STOREID AS UNSIGNED) ASC, d.itemname ASC')
            ->get();

        $groupedPicklist = $picklist->groupBy('STORENAME');

        return Inertia::render('Picklist/Index', ['groupedPicklist' => $groupedPicklist]);
    } */

    /* public function index()
    {
        $currentDateTime = Carbon::now()->toDateString();
        $storeId = Auth::user()->storeid;
        $role = Auth::user()->role;

        $picklist = DB::table('inventjournaltables AS b')
            ->select('b.journalid as JOURNALID', 'f.STOREID', 'b.POSTEDDATETIME', 'c.ADJUSTMENT', 'b.STOREID AS STORENAME',
                    'c.itemid AS ITEMID', 'd.itemname AS ITEMNAME', 'e.ITEMGROUP AS CATEGORY', 'c.COUNTED AS COUNTED',
                    DB::raw('(c.ADJUSTMENT) AS ACTUAL')) // Add this line
            ->leftJoin('inventjournaltrans AS c', 'b.JOURNALID', '=', 'c.JOURNALID')
            ->leftJoin('inventtables AS d', 'c.ITEMID', '=', 'd.itemid')
            ->leftJoin('rboinventtables AS e', 'd.ITEMID', '=', 'e.itemid')
            ->leftJoin('rbostoretables AS f', 'b.STOREID', '=', 'f.NAME')
            ->whereRaw("DATE(b.createddatetime) = ?", [$currentDateTime])
            ->where('b.POSTED', '=', '1')
            ->where('c.counted', '!=', '0')
            ->orderByRaw('CAST(f.STOREID AS UNSIGNED) ASC, d.itemname ASC')
            ->get();
        $groupedPicklist = $picklist->groupBy('STORENAME');

        $dr = DB::table('inventjournaltables AS b')
            ->select('b.journalid as JOURNALID', 'f.STOREID', 'b.POSTEDDATETIME', 'g.price as COST', 'c.ADJUSTMENT', 'b.STOREID AS STORENAME',
                    'c.itemid AS ITEMID', 'd.itemname AS ITEMNAME', 'e.ITEMGROUP AS CATEGORY', 'c.COUNTED AS COUNTED',
                    DB::raw('(c.ADJUSTMENT) AS ACTUAL')) 
            ->leftJoin('inventjournaltrans AS c', 'b.JOURNALID', '=', 'c.JOURNALID')
            ->leftJoin('inventtables AS d', 'c.ITEMID', '=', 'd.itemid')
            ->leftJoin('rboinventtables AS e', 'd.ITEMID', '=', 'e.itemid')
            ->leftJoin('rbostoretables AS f', 'b.STOREID', '=', 'f.NAME')
            ->leftJoin('inventtablemodules AS g', 'e.ITEMID', '=', 'g.ITEMID')
            ->whereRaw("DATE(b.createddatetime) = ?", [$currentDateTime])
            ->where('b.POSTED', '=', '1')
            ->where('c.counted', '!=', '0')
            ->orderByRaw('CAST(f.STOREID AS UNSIGNED) ASC, d.itemname ASC')
            ->get();
        $groupedDP = $dr->groupBy('STORENAME');

        return Inertia::render('Picklist/Index', ['groupedPicklist' => $groupedPicklist, 'dr',$groupedDP]);
    } */

    public function index()
    {
        $utcDateTime = Carbon::now('UTC');
        $currentDateTime = $utcDateTime->setTimezone('Asia/Manila')->toDateString();
        $storeId = Auth::user()->storeid;
        $role = Auth::user()->role;

        $picklist = DB::table('inventjournaltables AS b')
            ->select('b.journalid as JOURNALID', 'f.STOREID', 'b.POSTEDDATETIME', 'g.PRICE as COST', 'c.CHECKINGCOUNT', 'b.STOREID AS STORENAME',
                    'c.itemid AS ITEMID', 'd.itemname AS ITEMNAME', 'e.ITEMGROUP AS CATEGORY', 'c.ADJUSTMENT AS COUNTED',
                    DB::raw('(c.CHECKINGCOUNT) AS ACTUAL'))
            ->leftJoin('inventjournaltrans AS c', 'b.JOURNALID', '=', 'c.JOURNALID')
            ->leftJoin('inventtables AS d', 'c.ITEMID', '=', 'd.itemid')
            ->leftJoin('rboinventtables AS e', 'd.ITEMID', '=', 'e.itemid')
            ->leftJoin('rbostoretables AS f', 'b.STOREID', '=', 'f.NAME')
            ->leftJoin('inventtablemodules AS g', 'e.ITEMID', '=', 'g.ITEMID')
            ->whereRaw("DATE(b.createddatetime) = ?", [$currentDateTime])
            ->where('b.POSTED', '=', '1')
            
            ->where('c.counted', '!=', '0')
            ->where(function ($query) {
                $query->where('e.itemgroup', 'NOT LIKE', '%Cakes%')
                      ->where('e.itemgroup', 'NOT LIKE', '%Chilled Bites%');
            })
            ->orderByRaw('CAST(f.STOREID AS UNSIGNED) ASC, d.itemname ASC')
            ->get();
        $groupedPicklist = $picklist->groupBy('STORENAME');

        $picklist2 = DB::table('inventjournaltables AS b')
            ->select('b.journalid as JOURNALID', 'f.STOREID', 'b.POSTEDDATETIME', 'g.PRICE as COST', 'c.CHECKINGCOUNT', 'b.STOREID AS STORENAME',
                    'c.itemid AS ITEMID', 'd.itemname AS ITEMNAME', 'e.ITEMGROUP AS CATEGORY', 'c.ADJUSTMENT AS COUNTED',
                    DB::raw('(c.ADJUSTMENT) AS ACTUAL'))
            ->leftJoin('inventjournaltrans AS c', 'b.JOURNALID', '=', 'c.JOURNALID')
            ->leftJoin('inventtables AS d', 'c.ITEMID', '=', 'd.itemid')
            ->leftJoin('rboinventtables AS e', 'd.ITEMID', '=', 'e.itemid')
            ->leftJoin('rbostoretables AS f', 'b.STOREID', '=', 'f.NAME')
            ->leftJoin('inventtablemodules AS g', 'e.ITEMID', '=', 'g.ITEMID')
            ->whereRaw("DATE(b.createddatetime) = ?", [$currentDateTime])
            ->where('b.POSTED', '=', '1')
            ->where('c.counted', '!=', '0')
            ->where('c.VARIANTID', '=', 2)
            ->where(function ($query) {
                $query->where('e.itemgroup', 'NOT LIKE', '%Cakes%')
                      ->where('e.itemgroup', 'NOT LIKE', '%Chilled Bites%');
            })
            ->orderByRaw('CAST(f.STOREID AS UNSIGNED) ASC, d.itemname ASC')
            ->get();

        $groupedDR = $picklist2->groupBy('STORENAME');

        return Inertia::render('Picklist/Index', [
            'groupedPicklist' => $groupedPicklist,
            'groupedDR' => $groupedDR
        ]);
    }

    public function PickListInputData(Request $request)
    {
        $utcDateTime = Carbon::now('UTC');
        $currentDateTime = $utcDateTime->setTimezone('Asia/Manila')->toDateString();
        

        try {
            $affectedRows = inventjournaltrans::whereDate('transdate', $currentDateTime)
                ->update([
                    'CHECKINGCOUNT' => \DB::raw('ADJUSTMENT'),
                ]);
        
            if ($affectedRows > 0) {
                return redirect()->back()
                    ->with('message', 'Data updated successfully')
                    ->with('isSuccess', true);
            } else {
                return redirect()->back()
                    ->with('message', 'No matching records found for update')
                    ->with('isSuccess', false);
            }
        } catch (\Exception $e) {
            return back()
                ->withErrors([$e->getMessage()])
                ->withInput()
                ->with('isSuccess', false);
        }
    }

    public function updateActual(Request $request)
    {
        \Log::info('Incoming request:', $request->all());
        $request->validate([
            'journal_id' => 'required',
            'store_name' => 'required',
            'item_name' => 'required',
            'item_id' => 'required',
            'actual' => 'required|numeric',
        ]);

        $result = DB::table('inventjournaltrans')
            ->where('JOURNALID', $request->journal_id)
            ->where('ITEMID', $request->item_id)
            ->update(['CHECKINGCOUNT' => $request->actual]);

        if ($result) {
            $item = DB::table('inventjournaltrans')
                ->select('COUNTED', 'ADJUSTMENT')
                ->where('JOURNALID', $request->journal_id)
                ->where('ITEMID', $request->item_id)
                ->first();

            $actual = $item->COUNTED + $item->ADJUSTMENT;

            return response()->json([
                'success' => true,
                'actual' => $actual
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update adjustment'
            ], 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
