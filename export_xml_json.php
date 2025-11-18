<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Esportazione Json</title>
    <link rel="stylesheet" href="css/bootstrap.css">
</head>
<body>

<div class="container-fluid">
    <div class="row bg-primary bg-gradient text-white text-center py-4">
        <div class="col">
            <?php require 'inc/header.php'; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-4 col-md-3 bg-info bg-gradient text-dark py-4">
            <h3>Menù</h3>
            <?php require 'inc/menu.php'; ?>
        </div>
        
        <div class="col-8 col-md-9 py-4 bg-light">
            <?php
                $xmlPath = "xml/menu.xml";
                $xml = simplexml_load_file($xmlPath);

                $outputJson = "";   // contiene il JSON SOLO se serve
                $messaggio = "";    // messaggio di conferma per esportazione server

                if ($_SERVER["REQUEST_METHOD"] === "POST") {
                    $azione = $_POST["azione"];

                    // PRIMA: Esporta sul server
                    if ($azione === "esporta_server") {
                        $json = json_encode($xml, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                        file_put_contents(__DIR__."/xml/json/piatti.json", $json);
                        $messaggio = "<div class='alert alert-success text-center'>
                                        File salvato sul server in <b>xml/json</b>
                                      </div>";
                    }

                    // SECONDA: Esporta in locale
                    elseif ($azione === "esporta_locale") {
                        header('Content-Type: application/json');
                        header('Content-Disposition: attachment; filename="piatti.json"');
                        echo json_encode($xml, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                        exit;
                    }

                    // TERZA: Visualizza a schermo
                    elseif ($azione === "visualizza_json") {
                        $outputJson = json_encode($xml, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                    }
                }
            ?>

            <section class="bg-white p-4 rounded shadow-sm">
                <h2 class="mb-4">Esportazione Json</h2>

                <!-- Messaggio di conferma per esportazione server -->
                <?php echo $messaggio; ?>

                <form method="POST" class="text-center mb-4">
                    <button type="submit" name="azione" value="esporta_server" class="btn btn-primary px-3 mb-2">
                        Salva JSON sul Server
                    </button>
                    <button type="submit" name="azione" value="esporta_locale" class="btn btn-info px-3 mb-2">
                        Scarica JSON in Locale
                    </button>
                    <button type="submit" name="azione" value="visualizza_json" class="btn btn-success px-3 mb-2">
                        Visualizza JSON
                    </button>
                </form>

                <?php
                    if ($outputJson !== "") {
                        echo "<h4 class='text-center mt-4'>Contenuto JSON</h4>";
                        echo "<pre style='background:#f5f5f5; padding:15px; border-radius:10px; white-space:pre-wrap;'> $outputJson </pre>";
                    }
                ?>
            </section>
        </div>
    </div>

    <div class="row bg-dark bg-gradient text-white text-center py-4">
        <div class="col">
            <?php require 'inc/footer.php'; ?>
        </div>
    </div>
</div>

</body>
</html>