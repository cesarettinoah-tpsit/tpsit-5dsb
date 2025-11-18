<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>FORM GET</title>
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
            <form method="get" action="formrecive.php">
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome:</label>
                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Inserisci il nome" required>
                </div>
                <div class="mb-3">
                    <label for="cognome" class="form-label">Cognome:</label>
                    <input type="text" class="form-control" id="cognome" name="cognome" placeholder="Inserisci il cognome" required>
                </div>
                <button type="submit" class="btn btn-primary me-2">Invia</button>
                <button type="reset" class="btn btn-secondary">Cancella</button>
            </form>
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