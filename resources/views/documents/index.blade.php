@extends("layout")

@section("title")
Documents
@endsection

@section("content")
<?php
function bytesToHuman($bytes)
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    for ($i = 0; $bytes > 1024; $i++) { $bytes /= 1024;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}
?>

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
@endsection