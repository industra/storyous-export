<?php

function nastavDownloadHeaders($soubor, $delka = "0") {
    // required for IE, otherwise Content-disposition is ignored 
    if(ini_get('zlib.output_compression')) { ini_set('zlib.output_compression', 'Off'); }
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private",false);
    header("Content-Description: File Transfer");
    header("Content-Type: application/force-download");
    header("Content-Disposition: attachment; filename=\"$soubor\"");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: $delka");
    @set_time_limit(0);
}



$vystup = '';

if (isset($_POST['zpracuj_form'])) {
    $soubor_csv = $_FILES["soubor_csv"];
    $soubor_xml = $_FILES["soubor_xml"];
    $tmpfilename_csv = $soubor_csv['tmp_name'];
    $tmpfilename_xml = $soubor_xml['tmp_name'];
    if (is_uploaded_file($tmpfilename_csv) and is_uploaded_file($tmpfilename_xml)) {
        // vytvoreni cvs pole
        $radky = file($tmpfilename_csv);
        $csv_pole = array();
        foreach ($radky as $kk => $radek) {
            if ($kk == 0) { continue; }
            $csv_radek = str_getcsv($radek, ';');
            
            /* Likvidace zbytečných sloupců */
            /* odkomentuj nutné unset()y    */
            unset($csv_radek[4]);     // zruseni sloupce "stůl"
            //unset($csv_radek[6]);   // zruseni sloupce "obsluhoval"
            $csv_radek = array_merge($csv_radek);
            
            $csv_pole[$csv_radek[1]] = $csv_radek;
        }
        
        // vytvoreni xml pole
        /*
    <id>code:IC2017003636</id>*
    <kod>IC2017003636</kod>*
    <typPohybuK>typPohybu.prijem</typPohybuK>*
    <typDokl>code:POKLADNA</typDokl>*
    <datVyst>2017-09-10+02:00</datVyst>*
    <poznam>Fiskalizováno</poznam>*
    <sumDphSniz>15.6522</sumDphSniz>*
    <sumDphZakl>0</sumDphZakl>*
    <sumDphCelkem>15.6522</sumDphCelkem>*
    <sumCelkSniz>120</sumCelkSniz>*
    <sumCelkZakl>0</sumCelkZakl>*
    <sumCelkem>120</sumCelkem>*
    <bezPolozek>true</bezPolozek>*
    <zaokrJakSumK>zaokrJak.matem</zaokrJakSumK>*
    <zaokrNaSumK>zaokrNa.zadne</zaokrNaSumK>*
    <zaokrJakDphK>zaokrJak.matem</zaokrJakDphK>*
    <zaokrNaDphK>zaokrNa.zadne</zaokrNaDphK>*
        */
        $nactene_xml = file_get_contents($tmpfilename_xml);
        preg_match_all('|<pokladni-pohyb>.*<\/pokladni-pohyb>|Ums', $nactene_xml, $xml_polozky);
        $xml_pole = array();
        foreach ($xml_polozky[0] as $polozka) {
            $polozka_xml_pole = array();
            preg_match('|<id>(.*)</id>|U', $polozka, $data);
            $polozka_xml_pole['id'] = $data[1];
            preg_match('|<kod>(.*)</kod>|U', $polozka, $data);
            $kod = $data[1];
            $polozka_xml_pole['kod'] = $kod;
            
            // dekodujeme si kazdy dalsi radek v xml
            preg_match('|<typPohybuK>(.*)</typPohybuK>|U', $polozka, $data);
            $polozka_xml_pole['typPohybuK'] = $data[1];
            preg_match('|<typDokl>(.*)</typDokl>|U', $polozka, $data);
            $polozka_xml_pole['typDokl'] = $data[1];
            preg_match('|<datVyst>(.*)</datVyst>|U', $polozka, $data);
            $polozka_xml_pole['datVyst'] = $data[1];
            preg_match('|<poznam>(.*)</poznam>|U', $polozka, $data);
            $polozka_xml_pole['poznam'] = $data[1];
            preg_match('|<sumDphSniz>(.*)</sumDphSniz>|U', $polozka, $data);
            $polozka_xml_pole['sumDphSniz'] = $data[1];
            preg_match('|<sumDphZakl>(.*)</sumDphZakl>|U', $polozka, $data);
            $polozka_xml_pole['sumDphZakl'] = $data[1];
            preg_match('|<sumDphCelkem>(.*)</sumDphCelkem>|U', $polozka, $data);
            $polozka_xml_pole['sumDphCelkem'] = $data[1];
            preg_match('|<sumCelkSniz>(.*)</sumCelkSniz>|U', $polozka, $data);
            $polozka_xml_pole['sumCelkSniz'] = $data[1];
            preg_match('|<sumCelkZakl>(.*)</sumCelkZakl>|U', $polozka, $data);
            $polozka_xml_pole['sumCelkZakl'] = $data[1];
            preg_match('|<sumCelkem>(.*)</sumCelkem>|U', $polozka, $data);
            $polozka_xml_pole['sumCelkem'] = $data[1];
            preg_match('|<bezPolozek>(.*)</bezPolozek>|U', $polozka, $data);
            $polozka_xml_pole['bezPolozek'] = $data[1];
            preg_match('|<zaokrJakSumK>(.*)</zaokrJakSumK>|U', $polozka, $data);
            $polozka_xml_pole['zaokrJakSumK'] = $data[1];
            preg_match('|<zaokrNaSumK>(.*)</zaokrNaSumK>|U', $polozka, $data);
            $polozka_xml_pole['zaokrNaSumK'] = $data[1];
            preg_match('|<zaokrJakDphK>(.*)</zaokrJakDphK>|U', $polozka, $data);
            $polozka_xml_pole['zaokrJakDphK'] = $data[1];
            preg_match('|<zaokrNaDphK>(.*)</zaokrNaDphK>|U', $polozka, $data);
            $polozka_xml_pole['zaokrNaDphK'] = $data[1];
            
            $xml_pole[$kod] = $polozka_xml_pole;
        }
        
        // spojeni do noveho pole
        $spojene_pole = array();
        $chybi_v_xml = array();
        foreach ($csv_pole as $kk => $vv) {
            if (isset($xml_pole[$kk])) {
                $spojene_pole[$kk] = array_merge($vv, $xml_pole[$kk]);
            }
            else {
                $spojene_pole[$kk] = $vv;
                $chybi_v_xml[] = $kk;
            }
        }
        
        // vytvoreni noveho csv retezce
        $uvodni_polozky = array();
        $uvodni_polozky[] = '"Vytvořeno"';
        $uvodni_polozky[] = '"Číslo účtu"';
        $uvodni_polozky[] = '"Konečná cena"';
        $uvodni_polozky[] = '"Sleva"';
        $uvodni_polozky[] = '"Počet osob"';
        $uvodni_polozky[] = '"Typ platby"';
        $uvodni_polozky[] = '"Fiskalizováno"';
        $uvodni_polozky[] = '"Typ"';
        
        $uvodni_polozky[] = '"id"';
        $uvodni_polozky[] = '"kod"';
        $uvodni_polozky[] = '"typPohybuK"';
        $uvodni_polozky[] = '"typDokl"';
        $uvodni_polozky[] = '"datVyst"';
        $uvodni_polozky[] = '"poznam"';
        $uvodni_polozky[] = '"sumDphSniz"';
        $uvodni_polozky[] = '"sumDphZakl"';
        $uvodni_polozky[] = '"sumDphCelkem"';
        $uvodni_polozky[] = '"sumCelkSniz"';
        $uvodni_polozky[] = '"sumCelkZakl"';
        $uvodni_polozky[] = '"sumCelkem"';
        $uvodni_polozky[] = '"bezPolozek"';
        $uvodni_polozky[] = '"zaokrJakSumK"';
        $uvodni_polozky[] = '"zaokrNaSumK"';
        $uvodni_polozky[] = '"zaokrJakDphK"';
        $uvodni_polozky[] = '"zaokrNaDphK"';
        
        $vysledne_csv = implode(';', $uvodni_polozky);
        
        foreach ($spojene_pole as $polozka) {
            $radek_polozek = array();
            
            $radek_polozek[] = '"'.$polozka[0].'"';
            $radek_polozek[] = '"'.$polozka[1].'"';
            $radek_polozek[] = '"'.$polozka[2].'"';
            $radek_polozek[] = '"'.$polozka[3].'"';
            $radek_polozek[] = '"'.$polozka[4].'"';
            $radek_polozek[] = '"'.$polozka[5].'"';
            $radek_polozek[] = '"'.$polozka[6].'"';
            $radek_polozek[] = '"'.$polozka[7].'"';
            
            $radek_polozek[] = '"'.$polozka['id'].'"';
            $radek_polozek[] = '"'.$polozka['kod'].'"';
            $radek_polozek[] = '"'.$polozka['typPohybuK'].'"';
            $radek_polozek[] = '"'.$polozka['typDokl'].'"';
            $radek_polozek[] = '"'.$polozka['datVyst'].'"';
            $radek_polozek[] = '"'.$polozka['poznam'].'"';
            $radek_polozek[] = '"'.$polozka['sumDphSniz'].'"';
            $radek_polozek[] = '"'.$polozka['sumDphZakl'].'"';
            $radek_polozek[] = '"'.$polozka['sumDphCelkem'].'"';
            $radek_polozek[] = '"'.$polozka['sumCelkSniz'].'"';
            $radek_polozek[] = '"'.$polozka['sumCelkZakl'].'"';
            $radek_polozek[] = '"'.$polozka['sumCelkem'].'"';
            $radek_polozek[] = '"'.$polozka['bezPolozek'].'"';
            $radek_polozek[] = '"'.$polozka['zaokrJakSumK'].'"';
            $radek_polozek[] = '"'.$polozka['zaokrNaSumK'].'"';
            $radek_polozek[] = '"'.$polozka['zaokrJakDphK'].'"';
            $radek_polozek[] = '"'.$polozka['zaokrNaDphK'].'"';
            
            $vysledne_csv .= "\r\n".implode(';', $radek_polozek);
        }
        
        
        // vraceni retezce
        nastavDownloadHeaders('upraveny_export_'.time().'.csv', strlen($vysledne_csv));
        echo $vysledne_csv;
        exit;
    }
    else {
        $vystup = 'chybí jeden nebo oba soubory';
    }
}

header("Content-Type: text/html; charset=utf-8");

echo '<html>';
echo '
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE-edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway:200">
<link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-nn4HPE8lTHyVtfCBi5yW9d20FjT8BJwUXyWZT9InLYax14RDjBj46LmSztkmNP9w" crossorigin="anonymous">
 <style>
  * { font-family: "Raleway", "Helvetica Neue", Helvetica, Arial, sans-serif; }
  p { width: 50%; }
  body { padding-left: 5%; }
</style> 
</head>';

echo '<!doctype html>';
echo '<body>';
echo '<br /><br /><h1>Export účtenek ze Storyous "done right"</h1>';
echo '<p>Tato služba je k dispozici všem uživatelům pokladního systému Storyous, které trápí nekonzistentní a neúplné exporty
účtenek do tvaru, ve kterém je lze použít pro plnohodnotné účetnictví. Kód je volně dostupný pod GNU GPL v2 licencí na <a href="https://github.com/Industra/storyous-export">Githubu</a>.</p>
<p>Storyous nabízí 3 typy exportu účtenek:</P>
<ul>
<li>CSV soubor (export obsahuje stornované účtenky, rozlišuje platbu kartou a platbu hotově, neobsahuje rozlišení sazeb DPH)</li>
<li>Flexibee XML (export vynechává stornované účtenky, nerozlišuje platbu kartou a platbu hotově, obsahuje rozlišení sazeb DPH)</li>
<li>Pohoda XML (jako Flexibee)</li>
</ul>
<p>Výstupem této služby je CSV soubor obsahující exportované i stornované účtenky, rozlišení typu platby a rozlišení sazby DPH.
</p> 
<br />
<h2>Krok 1: exportuj</h2>
<p>Exportuj za stejné období účtenky ve formátu CSV a ve formátu Flexibee XML.</p>';

echo '<br />
<br />
<h2 style="">Krok 2: nahrej</h2>';
echo '<form method="post" action="" enctype="multipart/form-data" class="pure-form">
<input type="file" name="soubor_csv" />
        <label for="soubor_csv">
          ← Nahrej CSV soubor
        </label>

<br /><br />
<input type="file" name="soubor_xml" />
        <label for="soubor_xml">
          ← Nahrej XML (Flexibee) soubor
        </label>

<br /><br /><br />
<h2>Krok 3: Vytvoř a stáhni výsledek</h2>
<input type="submit" name="zpracuj_form" value="Vyrob společný CSV a stáhni ho"  class="pure-button pure-button-primary" />
<br /><br />
</form>';

if (!empty($vystup)) { echo '<div style="background-color: green; padding: 5px">'.$vystup.'</div>'; }

echo '</body>';
echo '</html>';

?>
