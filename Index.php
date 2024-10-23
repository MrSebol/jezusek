<?php
$host = 'localhost';   
$db   = 'stacjapaliw';  
$user = 'root';        
$pass = '';            

try {
    
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("set names utf8"); 
} catch (PDOException $e) {
   
    die("Błąd połączenia z bazą danych: " . $e->getMessage());
}
?>

<?php
include 'db.php'; 

$miasto = ''; 
$cena_od = 1.00; 
$cena_do = 10.00; 
$stacje = []; 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $miasto = $_POST['miasto']; 
    $cena_od = isset($_POST['cena_od']) ? $_POST['cena_od'] : $cena_od;
    $cena_do = isset($_POST['cena_do']) ? $_POST['cena_do'] : $cena_do;

    
    $sql = "SELECT stacje_paliw.nazwa, stacje_paliw.cena_paliwa, adresy.ulica, adresy.numer
            FROM stacje_paliw
            JOIN adresy ON stacje_paliw.adres = adresy.id
            WHERE adresy.miasto = :miasto AND stacje_paliw.cena_paliwa BETWEEN :cena_od AND :cena_do";

    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['miasto' => $miasto, 'cena_od' => $cena_od, 'cena_do' => $cena_do]);

    
    $stacje = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    
    $sql = "SELECT stacje_paliw.nazwa, stacje_paliw.cena_paliwa, adresy.ulica, adresy.numer
            FROM stacje_paliw
            JOIN adresy ON stacje_paliw.adres = adresy.id
            LIMIT 4";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    
    $stacje = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stacje Paliw</title>
    <link rel="stylesheet" href="styl.css"> 
</head>
<body>

<div class="container">
    <div class="sidebar">
        <div class="logo">
            <img src="orlen.png" alt="Logo Stacji Paliw"> 
        </div>
        <div class="filters">
            <h2>Filtry</h2>
            <form method="POST" action="index.php">
                <label for="miasto">Wybierz miasto:</label>
                <select name="miasto" id="miasto" required>
                    <option value="">--Wybierz miasto--</option>
                    <option value="Warszawa" <?php if($miasto == 'Warszawa') echo 'selected'; ?>>Warszawa</option>
                    <option value="Kraków" <?php if($miasto == 'Kraków') echo 'selected'; ?>>Kraków</option>
                    <option value="Gdańsk" <?php if($miasto == 'Gdańsk') echo 'selected'; ?>>Gdańsk</option>
                </select>

                <label for="cena_od">Cena za litr od:</label>
                <input type="range" id="cena_od" name="cena_od" min="1.00" max="10.00" step="0.01" value="<?php echo $cena_od; ?>" 
                oninput="this.nextElementSibling.value = this.value">
                <output><?php echo $cena_od; ?></output> zł

                <label for="cena_do">Cena za litr do:</label>
                <input type="range" id="cena_do" name="cena_do" min="1.00" max="10.00" step="0.01" value="<?php echo $cena_do; ?>" 
                oninput="this.nextElementSibling.value = this.value">
                <output><?php echo $cena_do; ?></output> zł

                <button type="submit">Szukaj</button>
                <button type="reset" onclick="window.location.href='index.php'">Reset</button> 
            </form>
        </div>
    </div>

    <div class="results">
        <h2>Wyniki wyszukiwania</h2>

        <?php if (empty($stacje)): ?>
            <p>Brak wyników dla wybranych filtrów.</p>
        <?php else: ?>
            <?php foreach ($stacje as $stacja): ?>
                <div class="result-item">
                    <h3><?php echo htmlspecialchars($stacja['nazwa']); ?></h3>
                    <p>Ulica: <?php echo htmlspecialchars($stacja['ulica']); ?>, Nr: <?php echo htmlspecialchars($stacja['numer']); ?></p>
                    <p>Cena za litr: <?php echo htmlspecialchars($stacja['cena_paliwa']); ?> zł</p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>


