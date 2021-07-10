<?php

namespace App\Http\Controllers;

use App\Domain;

use App\Exports\DomainsExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DomainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $domains = DB::table('domains')->orderBy("id")->get();
        return view("domains.index")
                ->with("domains", $domains);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("domains.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $this->validate(
            $request, [
            "title" => "required|min:1|max:30",
            "description" => "required"
            ]
        );
    
        $domain = new Domain();
        $domain->title = request("title");
        $domain->description = request("description");
        $domain-> save();
        return redirect("/domains");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Domain $domain
     * @return \Illuminate\Http\Response
     */
    public function show(Domain $domain)
    {
        return view("domains.show", compact("domain"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Domain $domain
     * @return \Illuminate\Http\Response
     */
    public function edit(Domain $domain)
    {
        return view("domains.edit", compact("domain"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Domain              $domain
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Domain $domain)
    {
        $this->validate(
            $request, [
            "title" => "required|min:1|max:30",
            "description" => "required"
            ]
        );
        $domain->title = request("title");
        $domain->description = request("description");
        $domain-> save();
        return redirect("/domains");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Domain $domain
     * @return \Illuminate\Http\Response
     */
    public function destroy(Domain $domain)
    {
        // dd($domain);
        $domain->delete();
        return redirect("/domains");
    }

    public function export() 
    {
        return Excel::download(new DomainsExport, 'domains.xlsx');
    }    

}
