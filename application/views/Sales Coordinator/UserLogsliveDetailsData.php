<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Live Camera View</title>
  <style>
    body {
      font-family: Arial;
      background: #f8f9fa;
      text-align: center;
      padding: 30px;
    }
    video {
      width: 70%;
      max-width: 600px;
      border-radius: 12px;
      box-shadow: 0 0 12px rgba(0,0,0,0.3);
    }
    .back-btn {
      display: inline-block;
      margin-top: 20px;
      padding: 8px 14px;
      background: #2ecc71;
      color: #fff;
      border-radius: 5px;
      text-decoration: none;
    }
    .back-btn:hover {
      background: #27ae60;
    }
  </style>
</head>
<body>
  <h2>Live Camera View for Log #<?= $log[0]->id ?></h2>
  <p>User IP: <strong><?= $log[0]->user_ip ?></strong></p>
  <video id="camera" autoplay playsinline></video><br>
  <a href="<?= base_url('camera/logs') ?>" class="back-btn">← Back to Logs</a>

  <script>
  async function startCamera() {
    try {
      const stream = await navigator.mediaDevices.getUserMedia({ video: true });
      document.getElementById('camera').srcObject = stream;
    } catch (error) {
      alert('Camera access denied or unavailable: ' + error.message);
    }
  }
  startCamera();
  </script>
</body>
</html>
