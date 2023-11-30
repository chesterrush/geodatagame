<?php
$host = "localhost";
$dbname = "koordinatenspiel";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Daten aus dem POST-Request holen
    $id = $_POST['id'] ?? '';
    $isCorrect = $_POST['isCorrect'] ?? '';
    $correctFormat = $_POST['correctFormat'] ?? null;

    // Update des aktuellen Eintrags
    $query = "UPDATE eingaben SET istKorrekt = :isCorrect WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':isCorrect', $isCorrect, PDO::PARAM_BOOL);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Wenn die Eingabe falsch war, fügen Sie eine neue korrekte Eingabe hinzu
    if ($isCorrect === 'false' && $correctFormat !== null) {
        // Holen Sie die ursprüngliche Eingabe
        $selectQuery = "SELECT koordinatenEingabe FROM eingaben WHERE id = :id";
        $selectStmt = $pdo->prepare($selectQuery);
        $selectStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $selectStmt->execute();
        $originalEingabe = $selectStmt->fetchColumn();

        // Fügen Sie eine neue Eingabe mit dem korrekten Format hinzu
        $insertQuery = "INSERT INTO eingaben (koordinatenEingabe, erkanntesFormat, istKorrekt) VALUES (:eingabe, :format, 1)";
        $insertStmt = $pdo->prepare($insertQuery);
        $insertStmt->bindParam(':eingabe', $originalEingabe);
        $insertStmt->bindParam(':format', $correctFormat);
        #$insertStmt->bindParam(':korrektesFormat', $correctFormat);
        $insertStmt->execute();
    }

    echo "Update erfolgreich durchgeführt";

} catch (PDOException $e) {
    die("Datenbankfehler: " . $e->getMessage());
}
?>
