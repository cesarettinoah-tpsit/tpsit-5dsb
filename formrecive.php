<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>FORM RECIVE</title>
    <link rel="stylesheet" href="css/bootstrap.css">
</head>
<body>

<div class="container-fluid">
    <div class="row bg-primary bg-gradient text-white text-center py-4">
        <div class="col">
            <?php require "inc/header.php"; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-4 col-md-3 bg-info bg-gradient text-dark py-4">
            <?php require "inc/menu.php"; ?>
        </div>
        
        <div class="col-8 col-md-9 py-4 bg-light">
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $nome = ($_GET['nome']);
                $cognome = ($_GET['cognome']);
            
                echo "<div class='alert alert-success text-center'>";
                echo "<h3>Benvenuto $nome $cognome!</h3>";
                echo "<p>Scegli un altra voce nel menu!.</p>";
                echo "<a href='formpost.php' class='btn btn-primary'>Torna al Form</a>";
                echo "</div>";
            } 
            ?>
        </div>
    </div>

    <div class="row bg-dark bg-gradient text-white text-center py-4">
        <div class="col">
            <?php require "inc/footer.php"; ?>
        </div>
    </div>
</div>

</body>
</html>