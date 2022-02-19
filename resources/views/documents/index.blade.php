@extends("layout")

@section("content")
<?php
function bytesToHuman($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    for ($i = 0; $bytes > 1024; $i++) $bytes /= 1024;
    return round($bytes, 2) . ' ' . $units[$i];
}
?>

<div class="p-3">
    <div data-role="panel" data-title-caption="Documents" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

    <ul>
        <li>
            Nombre de documents : {{ $count }}
        </li>
        <li>
            Taille totale : {{ bytesToHuman($sum) }} 
        </li>
    </ul>

    <br>
        <form action="/doc/check">
            <button type="submit" class="button success">Vérifier l'intégrité</button>            
        </form>
    </div>
</div>

@endsection