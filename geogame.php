<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$host = "localhost"; // oder Ihre spezifische Datenbankhost-Adresse
$dbname = "";
$username = "";
$password = "";

$formatierteKoordinate = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

    // Eingabedaten aus dem POST-Request holen und Schrägstriche entfernen
    $koordinatenEingabe = isset($_POST['koordinatenEingabe']) ? $_POST['koordinatenEingabe'] : '';
    

    // Regex für verschiedene Koordinatenformate
    $regexGaussKrueger = "/^([rR][:=]? ?[xX]? ?\s?\d{6,8}(?:[.,]\d{0,})?)\s*?[,-;\/\s]*?\s*?([hH][:=]? ?[yY]? ?\s?\d{5,7}(?:[.,]\d{0,})?)\s*$/";
    $regexGaussKrueger2 = "/^(\\d{6,8}(?:[.,]\\d{0,})?)\\s*?[,-;\\/\\s]*?\\s*?(\\d{5,7}(?:[.,]\\d{0,})?)\\s*$/";
    $regexWgs84Dezimal = "/^(-?[1-8]?\\d(,\\d+|\\.\\d+)?|90(,0+|\\.0+)?)[,; \\-]\\s*(-?1?[0-7]?\\d(,\\d+|\\.\\d+)?|180(,0+|\\.0+)?)([,; ]|$)/";
    $regexWgs84Dms = "/^(\\d{1,2}°\\s?\\d{1,2}'\\s?\\d{1,2}(\\.\\d{1,2})?\\s?['\"´`]{1,2}\\s?[nNsS],?\\s?;?\\s?)(\\d{1,2}°\\s?\\d{1,2}'\\s?\\d{1,2}(\\.\\d{1,2})?\\s?['\"´`]{1,2}\\s?[eEwW])$/";
    $regexWgs84Dm = "/^(\d{1,2}°\s?\d{1,2}(\.\d+)?\s?['\"´`]{0,2}\s?[nNsS])\s*[,; \-]?\s?(\d{1,2}°\s?\d{1,2}(\.\d+)?\s?['\"´`]{0,2}\s?[eEwW])$/";
    $regexMgrs = "/^\d{1,2}\s*[c-hj-np-xC-HJ-NP-X]\s*[a-zA-Z]{1,3}\s*\d{1,6}[\s,.-]*\d{1,6}$/";
    $regexUtm = "/^(Zone:?\s?|Z:?\s?)?(\\d{1,2}[c-hj-np-xC-HJ-NP-X])\\s*?(\\d{1,6}(?:[.,]\\d{0,})?)\\s*?[,-;\\/\\s]*?\\s*?(\\d{1,7}(?:[.,]\\d{0,})?)\\s*$/";
    $regexUtm2 = "/^(\\d{1,2}[c-hj-np-xC-HJ-NP-X])\\s*?(\\d{1,6}(?:[.,]\\d{0,})?)\\s*?[,-;\/\\s]*?\\s*?(\\d{1,7}(?:[.,]\\d{0,})?)\\s*/";
    $regexw3w = "/^\s*\w+\.\w+\.\w+\s*$/";
    

    // Koordinatenformat ermitteln
    $format = '';
    if (preg_match($regexGaussKrueger, $koordinatenEingabe, $matches)) {
        $format = 'Gauß-Krüger';
    } elseif (preg_match($regexGaussKrueger2, $koordinatenEingabe, $matches)) {
        $format = 'Gauß-Krüger';
    } elseif (preg_match($regexWgs84Dezimal, $koordinatenEingabe, $matches)) {
        $format = 'WGS84 Dezimal';
    } elseif(preg_match($regexWgs84Dms, $koordinatenEingabe, $matches)) {
        $format = 'WGS84 DMS';        
    } elseif (preg_match($regexWgs84Dm, $koordinatenEingabe, $matches)) {
        $format = 'WGS84 DM';
    } elseif (preg_match($regexMgrs, $koordinatenEingabe, $matches)) {
        $format = 'MGRS';
    } elseif (preg_match($regexUtm, $koordinatenEingabe, $matches)) {
        $format = 'UTM';
    } elseif (preg_match($regexUtm2, $koordinatenEingabe, $matches)) {
        $format = 'UTM';
    } elseif (preg_match($regexw3w, $koordinatenEingabe, $matches)) {
        return 'W3W';
    } else{
        $format = 'unknown';
    }

    // Daten in der Datenbank speichern
    $query = $pdo->prepare("INSERT INTO eingaben (koordinatenEingabe, erkanntesFormat, korrektesFormat) VALUES (:koordinatenEingabe, :format, :korrektesFormat)");
    $query->execute(['koordinatenEingabe' => $koordinatenEingabe, 'format' => $format, 'korrektesFormat' => $formatierteKoordinate]);
    $lastInsertId = $pdo->lastInsertId();

    // Antwort zusammenstellen
    $response = [
        'id' => $lastInsertId,
        'eingabe' => $koordinatenEingabe,
        'erkanntesFormat' => $format,
        'formatierteKoordinate' => $formatierteKoordinate
    ];


} catch (PDOException $e) {
    $response = ['error' => "Datenbankfehler: " . $e->getMessage()];
}


echo json_encode($response);
?>
