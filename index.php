<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>HOME</title>
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
            <h2 class="text-muted">Area Esercizio</h2>
        </div>
    </div>

    <div class="row bg-info bg-gradient text-white text-center py-4">
        <div class="col">
            <?php require "inc/footer.php"; ?>
        </div>
    </div>
</div>

</body>
</html>
