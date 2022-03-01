@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="Liste des contrôles" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">


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
                    <option value="99"
                        @if (Session::get("period")=="99")
                            selected 
                        @endif
                    >-- Choisir une période --</option>
                        @for ($i = -12; $i < 12; $i++)
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
                window.location = '/controls?domain=' + this.value;
            }, false);

            select = document.getElementById('cur_period');
            select.addEventListener('change', function(){
                window.location = '/controls?period=' + this.value;
            }, false);

            select = document.getElementById('status0');
            select.addEventListener('change', function(){
                window.location = '/controls?status=0';
            }, false);

            select = document.getElementById('status1');
            select.addEventListener('change', function(){
                window.location = '/controls?status=1';
            }, false);

            select = document.getElementById('status2');
            select.addEventListener('change', function(){
                window.location = '/controls?status=2';
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
                <th class="sortable-column" width="5%">Mesure</th>
                <th class="sortable-column" width="50%">Nom</th>
                <th class="sortable-column" width="5%">Note</th>
                <th class="sortable-column sort-asc"  width="5%">Planifié</th>
                <th class="sortable-column sort-asc"  width="5%">Réalisé</th>
                <th class="sortable-column"  width="5%">Suivant</th>
            </tr>
        </thead>
        <tbody>
    @foreach($controls as $control)
        <tr>
            <td>
                <a href="/domains/{{ $control->domain_id}} ">
                    {{ \App\Domain::find($control->domain_id)->title }}
                </a>
            </td>
            <td>
                <a href="/measures/{{ $control->measure_id }}">
                    {{ $control->clause }}
                </a>
            </td>
            <td>
                    {{ $control->name }} 
            </td>
            <td>
                <center>
                <a href="/controls/{{ $control->id }}">
                    @if ($control->score==1)
                        &#128545;
                    @elseif ($control->score==2)
                        &#128528;
                    @elseif ($control->score==3)
                        <span style="filter: sepia(1) saturate(5) hue-rotate(70deg)">&#128512;</span>
                    @else
                        &#9675; <!-- &#9899; -->
                    @endif
                </a>
                </center>
            </td>
            <td>
                <!-- format in red when month passed -->
                @if ($control->realisation_date == null)
                <a href="/control/show/{{$control->id}}">
                <b>
                    @if( strtotime($control->plan_date) >= strtotime('now') ) 
                        <font color="green">{{ $control->plan_date }}</font>
                    @else
                        <font color="red">{{ $control->plan_date }}</font>
                    @endif
                </b>
                </a>
                @else 
                    {{ $control->plan_date }}
                @endif
            </td>
            <td>
                <b>
                    <a href="/control/show/{{$control->id}}">
                        {{ $control->realisation_date }}
                    </a>
                </b>
            </td>
            <td>
                <b>
                    @if ($control->next_id!=null)
                    <a href="/controls/{{$control->next_id}}">
                        {{ $control->next_date }}
                    </a>
                    @endif
                </b>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
</div>
@endsection

