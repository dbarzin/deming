
@extends("layout")

@section("title")
Measurements
@endsection

@section("content")

    <div class="grid">
        <div class="row">
            <div class="cell-1" align="right">
                <strong>Domaine</strong>
            </div>
            <div class="cell-4">
                <select id='domain_id' name="domain_id" size="1" width='10'>
                    <option value="0">-- Choisir un domaine --</option>
                    @foreach ($domains as $domain)
                        <option value="{{ $domain->id }}"
                            @if (((int)Session::get("domain"))==$domain->id)        
                                selected 
                            @endif >
                            {{ $domain->title }} - {{ $domain->description }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="cell-1" align="right">
                <strong>Période</strong>
            </div>
            <div class="cell-2"> 
                <select id='cur_period' name="period" size="1" width='10'>
                    <option 
                            @if (Session::get("period")=="99")
                                selected 
                            @endif
                    value="99">-- Choisir une période --</option>
                        @for ($i = -6; $i < 6; $i++)
                            <option value="{{ $i }}"
                            @if (((int)Session::get("period"))==$i)        
                                selected 
                            @endif 
                            >
                            {{ now()->addMonth($i)->format("M Y") }}
                            </option>
                        @endfor
                    </select>
                </div>
            <div class="cell-1" align="right">
                <strong>Etat</strong>
            </div>
            <div >
                <input type="radio" data-role="radio" data-style="2" 
                name="status" value="0" id="status0"
                @if (Session::get("status")=="0" || Session::get("status")==null)
                checked
                @endif        
                > Tous
                <input type="radio" data-role="radio" data-style="2" 
                name="status" value="1" id="status1"
                @if (Session::get("status")=="1")
                checked
                @endif
                > Fait
                <input type="radio" data-role="radio" data-style="2" 
                name="status" value="2" id="status2"
                @if (Session::get("status")=="2")
                checked
                @endif        
                > A faire
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function(){
            var select = document.getElementById('domain_id');
            select.addEventListener('change', function(){
                window.location = '/measurements?domain=' + this.value;
            }, false);

            select = document.getElementById('cur_period');
            select.addEventListener('change', function(){
                window.location = '/measurements?period=' + this.value;
            }, false);

            select = document.getElementById('status0');
            select.addEventListener('change', function(){
                window.location = '/measurements?status=0';
            }, false);

            select = document.getElementById('status1');
            select.addEventListener('change', function(){
                window.location = '/measurements?status=1';
            }, false);

            select = document.getElementById('status2');
            select.addEventListener('change', function(){
                window.location = '/measurements?status=2';
            }, false);
        }, false);

    </script>

    <table class="table striped row-hover cell-border"
       data-role="table"
       data-rows="10"
       data-show-activity="true"
       data-rownum="false"
       data-check="false"
       data-check-style="1"
       >
        <thead>
            <tr>
                <th class="sortable-column" width="5%">Domaine</th>
                <th class="sortable-column" width="5%">Contrôle</th>
                <th class="sortable-column" width="50%">Nom</th>
                <th class="sortable-column" width="5%">Note</th>
                <th class="sortable-column sort-asc"  width="5%">Planifié</th>
                <th class="sortable-column sort-asc"  width="5%">Réalisé</th>
                <th class="sortable-column"  width="5%">Suivant</th>
            </tr>
        </thead>
        <tbody>
    @foreach($measurements as $measurement)
        <tr>
            <td>
                <a href="/domains/{{ $measurement->domain_id}} ">
                    {{ \App\Domain::find($measurement->domain_id)->title }}
                </a>
            </td>
            <td>
                <a href="/controls/{{ $measurement->control_id }}">
                    {{ $measurement->clause }}
                </a>
            </td>
            <td>
                    {{ $measurement->name }} 
            </td>
            <td>
                <center>
                <a href="/measurements/{{ $measurement->id }}">
                    @if ($measurement->score==1)
                        &#128545;
                    @elseif ($measurement->score==2)
                        &#128528;
                    @elseif ($measurement->score==3)
                        <span style="filter: sepia(1) saturate(5) hue-rotate(70deg)">&#128512;</span>
                    @else
                        &#9675; <!-- &#9899; -->
                    @endif
                </a>
                </center>
            </td>
            <td>
                <!-- format in red when month passed -->
                <a href="/measurement/show/{{$measurement->id}}">
                <b>
                @if ($measurement->realisation_date == null)
                    @if (
                    \Carbon\Carbon::
                    createFromFormat('Y-m-d',$measurement->plan_date)
                    ->addMonths(1)
                    ->startOfMonth()
                    ->isAfter(\Carbon\Carbon::now()))
                        <font color="green">
                    @else
                        <font color="red">
                    @endif
                @else
                    <font>
                @endif
                    {{ $measurement->plan_date }} 
                    </font>
                </b>
                </a>
            </td>
            <td>
                <b>
                    <a href="/measurement/show/{{$measurement->id}}">
                        {{ $measurement->realisation_date }}
                    </a>
                </b>
            </td>
            <td>
                <b>
                    @if ($measurement->next_id!=null)
                    <a href="/measurements/{{$measurement->next_id}}">
                        {{ $measurement->next_date }}
                    </a>
                    @endif
                </b>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection

