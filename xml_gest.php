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
        <div class="col">
            <?php require "inc/header.php"; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-4 col-md-3 bg-warning bg-gradient text-dark py-4">
            <?php require "inc/menu.php"; ?>
        </div>
        
        <div class="col-8 col-md-9 py-4">
            <h2 class="text-muted mb-4">Gestione Menu Ristorante</h2>
            
            <?php
            function validaControXSD($dati) {
                $xsdFile = 'xml/schema.xsd';
                
                if (!file_exists($xsdFile)) {
                    return "File schema.xsd non trovato";
                }
                
                $xmlTemp = new SimpleXMLElement('<?xml version="1.0"?><menu></menu>');
                $piatto = $xmlTemp->addChild('piatto');
                $piatto->addChild('nome', $dati['nome']);
                $piatto->addChild('tipologia', $dati['tipologia']);
                $piatto->addChild('descrizione', $dati['descrizione']);
                $piatto->addChild('prezzo', $dati['prezzo']);
                $piatto->addChild('vegetariano', $dati['vegetariano']);
                $piatto->addChild('vegano', $dati['vegano']);
                
                libxml_use_internal_errors(true);
                $dom = new DOMDocument();
                $dom->loadXML($xmlTemp->asXML());
                
                if ($dom->schemaValidate($xsdFile)) {
                    return true;
                } else {
                    $errors = libxml_get_errors();
                    $errorMsg = "Validazione XSD fallita: ";
                    if (!empty($errors)) {
                        $errorMsg .= $errors[0]->message;
                    }
                    libxml_clear_errors();
                    return $errorMsg;
                }
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome'])) {
                $xmlFile = 'xml/menu.xml';
                
                $nome = trim($_POST['nome']);
                $tipologia = trim($_POST['tipologia']);
                $descrizione = trim($_POST['descrizione']);
                $prezzo = floatval($_POST['prezzo']);
                $vegetariano = isset($_POST['vegetariano']) ? 'si' : 'no';
                $vegano = isset($_POST['vegano']) ? 'si' : 'no';
                
                $validazioneConfermata = isset($_POST['conferma_validazione']) && $_POST['conferma_validazione'] === 'si';
                
                if (!$validazioneConfermata) {
                    echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                            Devi confermare la validazione XSD prima di salvare.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                          </div>';
                } else {
                    if (!empty($nome) && !empty($tipologia) && !empty($descrizione) && $prezzo > 0) {
                        
                        $datiPiatto = [
                            'nome' => $nome,
                            'tipologia' => $tipologia,
                            'descrizione' => $descrizione,
                            'prezzo' => number_format($prezzo, 2, '.', ''),
                            'vegetariano' => $vegetariano,
                            'vegano' => $vegano
                        ];
                        
                        $validazioneXSD = validaControXSD($datiPiatto);
                        
                        if ($validazioneXSD === true) {
                            if (!is_dir('xml')) {
                                mkdir('xml', 0755, true);
                            }
                            
                            if (file_exists($xmlFile)) {
                                $xml = simplexml_load_file($xmlFile);
                                if ($xml === false) {
                                    $xml = new SimpleXMLElement('<?xml version="1.0"?><menu></menu>');
                                }
                            } else {
                                $xml = new SimpleXMLElement('<?xml version="1.0"?><menu></menu>');
                            }
                            
                            $piatto = $xml->addChild('piatto');
                            $piatto->addChild('nome', htmlspecialchars($nome));
                            $piatto->addChild('tipologia', htmlspecialchars($tipologia));
                            $piatto->addChild('descrizione', htmlspecialchars($descrizione));
                            $piatto->addChild('prezzo', number_format($prezzo, 2, '.', ''));
                            $piatto->addChild('vegetariano', $vegetariano);
                            $piatto->addChild('vegano', $vegano);
                            
                            $xmlString = $xml->asXML();
                            if (file_put_contents($xmlFile, $xmlString) !== false) {
                                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                        Piatto validato e aggiunto al menu.
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                      </div>';
                            } else {
                                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        Impossibile salvare il piatto.
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                      </div>';
                            }
                        } else {
                            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    ' . $validazioneXSD . '
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                  </div>';
                        }
                        
                    } else {
                        echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                                Compila tutti i campi correttamente.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                              </div>';
                    }
                }
            }
            
            if (isset($_GET['elimina'])) {
                $id = intval($_GET['elimina']);
                $xmlFile = 'xml/menu.xml';
                
                if (file_exists($xmlFile)) {
                    $xml = simplexml_load_file($xmlFile);
                    if ($xml && isset($xml->piatto[$id])) {
                        
                        $newXml = new SimpleXMLElement('<?xml version="1.0"?><menu></menu>');
                        
                        $index = 0;
                        foreach ($xml->piatto as $piatto) {
                            if ($index != $id) {
                                $newPiatto = $newXml->addChild('piatto');
                                $newPiatto->addChild('nome', (string)$piatto->nome);
                                $newPiatto->addChild('tipologia', (string)$piatto->tipologia);
                                $newPiatto->addChild('descrizione', (string)$piatto->descrizione);
                                $newPiatto->addChild('prezzo', (string)$piatto->prezzo);
                                $newPiatto->addChild('vegetariano', (string)$piatto->vegetariano);
                                $newPiatto->addChild('vegano', (string)$piatto->vegano);
                            }
                            $index++;
                        }
                        
                        if ($newXml->asXML($xmlFile)) {
                            echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    Piatto eliminato!
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                  </div>';
                        }
                    }
                }
            }
            ?>
            
            <div class="card shadow-sm mb-5">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">Aggiungi Nuovo Piatto</h3>
                </div>
                <div class="card-body">
                    <form action="" method="POST" id="piattoForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nome" class="form-label">Nome Piatto</label>
                                <input type="text" class="form-control" id="nome" name="nome" placeholder="Inserisci il nome del piatto">
                            </div>
                            <div class="col-md-6">
                                <label for="tipologia" class="form-label">Tipologia</label>
                                <select class="form-select" id="tipologia" name="tipologia">
                                    <option value="" selected disabled>Seleziona Tipologia</option>
                                    <option value="Antipasto">Antipasto</option>
                                    <option value="Primo">Primo</option>
                                    <option value="Secondo">Secondo</option>
                                    <option value="Dolce">Dolce</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="descrizione" class="form-label">Descrizione</label>
                                <textarea class="form-control" id="descrizione" name="descrizione" rows="3" placeholder="Descrivi il piatto, ingredienti..."></textarea>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="prezzo" class="form-label">Prezzo (€)</label>
                                <div class="input-group">
                                    <span class="input-group-text">€</span>
                                    <input type="number" class="form-control" id="prezzo" name="prezzo" min="0.01" step="0.01" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Opzioni Dietetiche</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="vegetariano" id="vegetariano" value="si">
                                    <label class="form-check-label" for="vegetariano">Vegetariano</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="vegano" id="vegano" value="si">
                                    <label class="form-check-label" for="vegano">Vegano</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-warning">
                                    <div class="card-body">
                                        <h6 class="card-title text-warning">Validazione XSD</h6>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="conferma_validazione" id="conferma_validazione" value="si">
                                            <label class="form-check-label fw-bold" for="conferma_validazione">
                                                Confermo che il piatto rispetta lo schema XSD
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-outline-secondary me-md-2">Pulisci</button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">Aggiungi al Menu</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title mb-0">Menu del Ristorante</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nome</th>
                                    <th>Tipologia</th>
                                    <th>Descrizione</th>
                                    <th>Prezzo</th>
                                    <th>Vegetariano</th>
                                    <th>Vegano</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $xmlFile = 'xml/menu.xml';
                                if (file_exists($xmlFile)) {
                                    $xml = simplexml_load_file($xmlFile);
                                    if ($xml) {
                                        $index = 0;
                                        
                                        foreach ($xml->piatto as $piatto) {
                                            $badgeClass = [
                                                'Antipasto' => 'bg-info',
                                                'Primo' => 'bg-warning text-dark',
                                                'Secondo' => 'bg-danger',
                                                'Dolce' => 'bg-success'
                                            ];
                                            
                                            $tipologia = (string)$piatto->tipologia;
                                            $badgeColor = isset($badgeClass[$tipologia]) ? $badgeClass[$tipologia] : 'bg-secondary';
                                            
                                            echo '<tr>';
                                            echo '<td class="fw-bold">' . htmlspecialchars($piatto->nome) . '</td>';
                                            echo '<td><span class="badge ' . $badgeColor . '">' . htmlspecialchars($tipologia) . '</span></td>';
                                            echo '<td>' . htmlspecialchars($piatto->descrizione) . '</td>';
                                            echo '<td class="fw-bold text-success">€' . htmlspecialchars($piatto->prezzo) . '</td>';
                                            echo '<td>' . ($piatto->vegetariano == 'si' ? '<span class="badge bg-success">✓</span>' : '<span class="badge bg-danger">✗</span>') . '</td>';
                                            echo '<td>' . ($piatto->vegano == 'si' ? '<span class="badge bg-success">✓</span>' : '<span class="badge bg-danger">✗</span>') . '</td>';
                                            echo '<td>';
                                            echo '<a href="?elimina=' . $index . '" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'Sei sicuro di voler eliminare ' . htmlspecialchars($piatto->nome) . '?\')">Elimina</a>';
                                            echo '</td>';
                                            echo '</tr>';
                                            $index++;
                                        }
                                        
                                        if ($index === 0) {
                                            echo '<tr><td colspan="7" class="text-center text-muted py-4">Nessun piatto nel menu.</td></tr>';
                                        }
                                    }
                                } else {
                                    echo '<tr><td colspan="7" class="text-center text-muted py-4">Menu non ancora creato. Aggiungi il primo piatto!</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row bg-info bg-gradient text-white text-center py-4">
        <div class="col">
            <?php require "inc/footer.php"; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.getElementById('piattoForm').addEventListener('submit', function(e) {
    const prezzo = parseFloat(document.getElementById('prezzo').value);
    const validazioneConfermata = document.getElementById('conferma_validazione').checked;
    
    if (prezzo <= 0 || isNaN(prezzo)) {
        e.preventDefault();
        alert('Il prezzo deve essere maggiore di 0!');
        return false;
    }
    
    if (!validazioneConfermata) {
        e.preventDefault();
        alert('Devi confermare la validazione XSD prima di procedere!');
        return false;
    }
});
</script>
</body>
</html>