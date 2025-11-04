<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>FORM POST</title>
    <link rel="stylesheet" href="css/bootstrap.css">
</head>
<body>

<div class="container-fluid">
    <div class="row bg-secondary text-white text-center py-4">
        <div class="col">
            <?php require "inc/header.php"; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-4 col-md-3 bg-warning bg-gradient text-dark py-4">
            <?php require "inc/menu.php"; ?>
        </div>
        
        <div class="col-8 col-md-9 py-4">
            <?php
            
            if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['nome']) && isset($_GET['cognome'])) {
                $nome = htmlspecialchars(trim($_GET['nome']));
                $cognome = htmlspecialchars(trim($_GET['cognome']));
                
                
                echo "<div class='alert alert-success text-center'>";
                echo "<h3>Benvenuto $nome $cognome!</h3>";
                echo "<p>Scegli un altra voce nel menu!.</p>";
                echo "<a href='formpost.php' class='btn btn-primary'>Torna al Form</a>";
                echo "</div>";
            } else {
                
            ?>
                <form method="GET" action="formpost.php">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome:</label>
                        <input type="text" class="form-control" id="nome" name="nome" placeholder="Inserisci il nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="cognome" class="form-label">Cognome:</label>
                        <input type="text" class="form-control" id="cognome" name="cognome" placeholder="Inserisci il cognome" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Invia</button>
                    <button type="reset" class="btn btn-secondary">Cancella</button>
                </form>
            <?php
            }
            ?>
        </div>


    <div class="row bg-info bg-gradient text-white text-center py-4">
        <div class="col">
            <?php require "inc/footer.php"; ?>
        </div>
    </div>
</div>

</body>
</html>
