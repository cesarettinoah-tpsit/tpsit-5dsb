<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Menu XML</title>
    <link rel="stylesheet" href="css/bootstrap.css">
</head>
<body>

<div class="container-fluid">
    <div class="row bg-secondary text-white text-center py-4">
        <div class="col"><?php require "inc/header.php"; ?></div>
    </div>

    <div class="row">
        <div class="col-4 col-md-3 bg-warning bg-gradient text-dark py-4">
            <?php require "inc/menu.php"; ?>
        </div>
        
        <div class="col-8 col-md-9 py-4">
            <h2 class="text-muted mb-4">Gestione Menu</h2>
            
            <?php
            // Funzione validazione XSD
            function validaXSD($dati) {
                if (!file_exists('xml/schema.xsd')) return "Schema XSD non trovato";
                
                $xml = new SimpleXMLElement('<?xml version="1.0"?><menu></menu>');
                $piatto = $xml->addChild('piatto');
                foreach($dati as $key => $value) $piatto->addChild($key, $value);
                
                libxml_use_internal_errors(true);
                $dom = new DOMDocument();
                $dom->loadXML($xml->asXML());
                
                if ($dom->schemaValidate('xml/schema.xsd')) return true;
                
                $errors = libxml_get_errors();
                libxml_clear_errors();
                return $errors[0]->message ?? 'Errore validazione';
            }

            // Gestione POST
            if ($_POST) {
                $nome = trim($_POST['nome'] ?? '');
                $tipologia = trim($_POST['tipologia'] ?? '');
                $descrizione = trim($_POST['descrizione'] ?? '');
                $prezzo = floatval($_POST['prezzo'] ?? 0);
                $vegetariano = $_POST['vegetariano'] ?? 'no';
                $vegano = $_POST['vegano'] ?? 'no';
                
                if (isset($_POST['validate'])) {
                    // Validazione
                    if ($nome && $tipologia && $descrizione && $prezzo > 0) {
                        $dati = [
                            'nome' => $nome,
                            'tipologia' => $tipologia,
                            'descrizione' => $descrizione,
                            'prezzo' => number_format($prezzo, 2),
                            'vegetariano' => $vegetariano,
                            'vegano' => $vegano
                        ];
                        
                        $validazione = validaXSD($dati);
                        
                        if ($validazione === true) {
                            echo '<div class="alert alert-success">Validazione XSD completata con successo!</div>';
                        } else {
                            echo '<div class="alert alert-danger">' . $validazione . '</div>';
                        }
                    } else {
                        echo '<div class="alert alert-warning">Compila tutti i campi obbligatori.</div>';
                    }
                }
                elseif (isset($_POST['submit'])) {
                    // Salvataggio
                    if ($nome && $tipologia && $descrizione && $prezzo > 0) {
                        $dati = [
                            'nome' => htmlspecialchars($nome),
                            'tipologia' => htmlspecialchars($tipologia),
                            'descrizione' => htmlspecialchars($descrizione),
                            'prezzo' => number_format($prezzo, 2),
                            'vegetariano' => $vegetariano,
                            'vegano' => $vegano
                        ];
                        
                        if (!is_dir('xml')) mkdir('xml', 0755, true);
                        
                        $xml = file_exists('xml/menu.xml') ? 
                            simplexml_load_file('xml/menu.xml') : 
                            new SimpleXMLElement('<?xml version="1.0"?><menu></menu>');
                        
                        $piatto = $xml->addChild('piatto');
                        foreach($dati as $key => $value) $piatto->addChild($key, $value);
                        
                        if ($xml->asXML('xml/menu.xml')) {
                            echo '<div class="alert alert-success">Piatto aggiunto al menu!</div>';
                            // Reset dei valori dopo il salvataggio
                            $nome = $tipologia = $descrizione = '';
                            $prezzo = 0;
                            $vegetariano = $vegano = 'no';
                        } else {
                            echo '<div class="alert alert-danger">Errore nel salvataggio.</div>';
                        }
                    } else {
                        echo '<div class="alert alert-warning">Compila tutti i campi obbligatori.</div>';
                    }
                }
            }

            // Eliminazione
            if (isset($_GET['elimina'])) {
                $id = intval($_GET['elimina']);
                if (file_exists('xml/menu.xml') && ($xml = simplexml_load_file('xml/menu.xml'))) {
                    unset($xml->piatto[$id]);
                    if ($xml->asXML('xml/menu.xml')) {
                        echo '<div class="alert alert-warning">Piatto eliminato!</div>';
                    }
                }
            }
            ?>
            
            <!-- Form -->
            <div class="card shadow-sm mb-5">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">Aggiungi Nuovo Piatto</h3>
                </div>
                <div class="card-body">
                    <form method="POST" id="piattoForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nome Piatto</label>
                                <input type="text" class="form-control" name="nome" value="<?= htmlspecialchars($nome ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tipologia</label>
                                <select class="form-select" name="tipologia" required>
                                    <option value="" disabled <?= !isset($tipologia) ? 'selected' : '' ?>>Seleziona</option>
                                    <option value="Antipasto" <?= ($tipologia ?? '') == 'Antipasto' ? 'selected' : '' ?>>Antipasto</option>
                                    <option value="Primo" <?= ($tipologia ?? '') == 'Primo' ? 'selected' : '' ?>>Primo</option>
                                    <option value="Secondo" <?= ($tipologia ?? '') == 'Secondo' ? 'selected' : '' ?>>Secondo</option>
                                    <option value="Dolce" <?= ($tipologia ?? '') == 'Dolce' ? 'selected' : '' ?>>Dolce</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label">Descrizione</label>
                                <textarea class="form-control" name="descrizione" rows="3" required><?= htmlspecialchars($descrizione ?? '') ?></textarea>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Prezzo (€)</label>
                                <input type="number" class="form-control" name="prezzo" value="<?= $prezzo ?? '' ?>" min="0.01" step="0.01">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Opzioni</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="vegetariano" value="si" <?= ($vegetariano ?? '') == 'si' ? 'checked' : '' ?>>
                                    <label class="form-check-label">Vegetariano</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="vegano" value="si" <?= ($vegano ?? '') == 'si' ? 'checked' : '' ?>>
                                    <label class="form-check-label">Vegano</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-outline-secondary me-2">Pulisci</button>
                            <button type="submit" name="validate" class="btn btn-warning me-2">Validate</button>
                            <button type="submit" name="submit" class="btn btn-primary">Aggiungi</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Tabella Menu -->
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title mb-0">Menu del Ristorante</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nome</th>
                                    <th>Tipologia</th>
                                    <th>Descrizione</th>
                                    <th>Prezzo</th>
                                    <th>Veg</th>
                                    <th>Vegan</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (file_exists('xml/menu.xml') && ($xml = simplexml_load_file('xml/menu.xml'))) {
                                    foreach ($xml->piatto as $index => $piatto) {
                                        echo '<tr>
                                            <td>' . htmlspecialchars($piatto->nome) . '</td>
                                            <td><span class="badge bg-secondary">' . htmlspecialchars($piatto->tipologia) . '</span></td>
                                            <td>' . htmlspecialchars($piatto->descrizione) . '</td>
                                            <td>€' . htmlspecialchars($piatto->prezzo) . '</td>
                                            <td>' . ($piatto->vegetariano == 'si' ? '✓' : '✗') . '</td>
                                            <td>' . ($piatto->vegano == 'si' ? '✓' : '✗') . '</td>
                                            <td><a href="?elimina=' . $index . '" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'Eliminare?\')">Elimina</a></td>
                                        </tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="7" class="text-center py-4">Nessun piatto nel menu.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row bg-info text-white text-center py-4">
        <div class="col"><?php require "inc/footer.php"; ?></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>