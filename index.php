<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type"
    content="text/html; charset=utf-8">
  <meta name="viewport"
    content="width=device-width, user-scalable=no">
  <meta name="apple-mobile-web-app-title" content="BW Bayern">

  <title>Pages</title>

  <link rel="icon" href="https://pages.services.bergwacht-bayern.org/shared/favicon.ico" sizes="any">
  <link rel="icon" href="https://pages.services.bergwacht-bayern.org/shared/site-icon.svg" type="image/svg+xml">
  <link rel="apple-touch-icon" href="https://pages.services.bergwacht-bayern.org/shared/site-icon.png">
  <link rel="manifest" href="https://pages.services.bergwacht-bayern.org/shared/site.webmanifest">

  <link rel="stylesheet" type="text/css" href="https://pages.services.bergwacht-bayern.org/shared/base+bootstrap.css">
  <link rel="stylesheet" type="text/css" href="https://pages.services.bergwacht-bayern.org/shared/fa/css/all.min.css" media="all">



  <script src="https://pages.services.bergwacht-bayern.org/shared/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</head>

<body>
  <div id="kiosk">
    <a class="back-button" href="https://passport.services.bergwacht-bayern.org">
      <i class="fa fa-arrow-left"></i>
    </a>

    <a href="https://passport.services.bergwacht-bayern.org">
      <img class="logo" src="https://pages.services.bergwacht-bayern.org/shared/framed-logo.svg">
    </a>


    




<!-- Modal für die Auswahl des korrekten Formats -->
<div class="modal fade" id="formatCorrectionModal" tabindex="-1" aria-labelledby="formatCorrectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formatCorrectionModalLabel">Korrekte Format auswählen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <select class="form-select" id="correctFormatSelect">
                    <option value="">Bitte Wählen</option>
                    <option value="Gauß-Krüger">Gauß-Krüger</option>
                    <option value="WGS84 Dezimal">WGS84 Dezimal</option>
                    <option value="WGS84 DMS">WGS84 DMS</option>
                    <option value="WGS84 DM">WGS84 DM</option>
                    <option value="MGRS">MGRS</option>
                    <option value="UTM">UTM</option>
                    <option value="W3W">W3W</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="saveCorrectFormatBtn">Speichern</button>
            </div>
        </div>
    </div>
</div>


<div class="container mt-5">
    <h2>Koordinaten Spiel</h2>
    <form id="koordinatenForm" method="post">
        <div class="mb-3">
            <label for="koordinatenEingabe" class="form-label">Koordinaten eingeben:</label>
            <input type="text" class="form-control" id="koordinatenEingabe" name="koordinatenEingabe" required>
        </div>
        <button type="submit" class="btn btn-primary">Senden</button>
    </form>

    <div id="responseArea" class="mt-4" style="display:none;">
        <p id="responseText"></p>
        <button id="correctBtn" class="btn btn-success">Ja, richtig</button>
        <button id="incorrectBtn" class="btn btn-danger">Nein, falsch</button>
    </div>
</div>





    <div class="footer">
      <div>
        <span>© Bergwacht Bayern | </span>
        <a href="https://www.bergwacht-bayern.de/impressum.html">
          Impressum
        </a>
        <span> | </span>
        <a href="https://www.bergwacht-bayern.de/datenschutz.html">
          Datenschutz
        </a>
      </div>
    </div>
  </div>
</body>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
<script>

document.getElementById('koordinatenForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);
    fetch('geogame.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('responseText').textContent = `Eingabe: ${data.eingabe}, Erkanntes Format: ${data.erkanntesFormat}`;
        document.getElementById('responseArea').style.display = 'block';
        sessionStorage.setItem('lastInsertId', data.id);
    });
});

document.getElementById('correctBtn').addEventListener('click', function() {
    sendFeedback(true);
});

document.getElementById('incorrectBtn').addEventListener('click', function() {
    new bootstrap.Modal(document.getElementById('formatCorrectionModal')).show();
});

document.getElementById('saveCorrectFormatBtn').addEventListener('click', function() {
    const correctFormat = document.getElementById('correctFormatSelect').value;
    sendFeedback(false, correctFormat);
    bootstrap.Modal.getInstance(document.getElementById('formatCorrectionModal')).hide();
});

function sendFeedback(isCorrect, correctFormat = null) {
    const id = sessionStorage.getItem('lastInsertId');
    const bodyData = `id=${id}&isCorrect=${isCorrect}`;
    const bodyDataWithFormat = correctFormat ? bodyData + `&correctFormat=${correctFormat}` : bodyData;

    fetch('updategame.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: bodyDataWithFormat
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
        document.getElementById('koordinatenForm').reset();
        document.getElementById('responseArea').style.display = 'none';
    });
}

    
</script>


</html>

