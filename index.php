<?php
    require("config.php");
    $mydb = new mysqli(SERVER, UTENTE, PASSWORD, DATABASE);
    if ($mydb->connect_errno) {
        echo "Errore nella connessione a MySQL: (" . $mydb->connect_errno . ") " . $mydb->connect_error;
        exit();
    }

    // Aggiunta di un libro
    if (isset($_POST['titolo'], $_POST['autore'], $_POST['prezzo'], $_POST['quantita'])) {
        $stmt = $mydb->prepare("INSERT INTO Libri (titolo, autore, prezzo, quantita) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssdi", $_POST['titolo'], $_POST['autore'], $_POST['prezzo'], $_POST['quantita']);
        $stmt->execute();
    }

    // Acquisto di un libro
    if (isset($_POST['id_libro'], $_POST['quantita_acquistata'])) {
        // prende la quantita
        $stmt = $mydb->prepare("SELECT quantita FROM Libri WHERE id = ?");
        $stmt->bind_param("i", $_POST['id_libro']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $current_quantita = $row['quantita'];

        // verifica la quantità
        if ($_POST['quantita_acquistata'] > $current_quantita) {
            echo "<script>alert('La quantità richiesta non è disponibile');</script>";
        } else {
            $stmt = $mydb->prepare("INSERT INTO Acquisti (id_libro, data_acquisto, quantita_acquistata) VALUES (?, NOW(), ?)");
            $stmt->bind_param("ii", $_POST['id_libro'], $_POST['quantita_acquistata']);
            $stmt->execute();
        }
    }

    // Recupero dei libri
    $result = $mydb->query("SELECT * FROM Libri");

    // Mostra i libri
    if ($result->num_rows > 0) {
        echo "<h1>Elenco Libri</h1>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Titolo</th><th>Autore</th><th>Prezzo</th><th>Quantità</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['titolo'] . "</td>";
            echo "<td>" . $row['autore'] . "</td>";
            echo "<td>" . $row['prezzo'] . "</td>";
            echo "<td>" . $row['quantita'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Nessun libro presente.";
    }

    // Recupero degli acquisti
    $result = $mydb->query("SELECT * FROM Acquisti");

    // Mostra gli acquisti
    if ($result->num_rows > 0) {
        echo "<h1>Elenco Acquisti</h1>";
        echo "<table>";
        echo "<tr><th>ID Libro</th><th>Data Acquisto</th><th>Quantità Acquistata</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id_libro'] . "</td>";
            echo "<td>" . $row['data_acquisto'] . "</td>";
            echo "<td>" . $row['quantita_acquistata'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Nessun acquisto effettuato.";
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Inserimento Libro</h1>
    <form action="index.php" method="POST">
        <label for="titolo">Titolo:</label>
        <input type="text" name="titolo" id="titolo" required><br><br>
        <label for="autore">Autore:</label>
        <input type="text" name="autore" id="autore" required><br><br>
        <label for="prezzo">Prezzo:</label>
        <input type="number" name="prezzo" id="prezzo" required><br><br>
        <label for="quantita">Quantità:</label>
        <input type="number" name="quantita" id="quantita" required><br><br>
        <input type="submit" value="Aggiungi Libro">
    </form>
    <h1>Acquisto Libro</h1>
    <form action="index.php" method="POST">
        <label for="id_libro">ID Libro:</label>
        <input type="number" name="id_libro" id="id_libro" required><br><br>
        <label for="quantita_acquistata">Quantità Acquistata:</label>
        <input type="number" name="quantita_acquistata" id="quantita_acquistata" required><br><br>
        <input type="submit" value="Acquista Libro">
    </form>
</body>
</html>
