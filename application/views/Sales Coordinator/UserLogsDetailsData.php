<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title> Logs</title>
  <style>
    table { border-collapse: collapse; width: 90%; margin: 20px auto; font-family: Arial; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background: #f4f4f4; }
    .view-btn {
      padding: 5px 10px;
      background: #3498db;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .view-btn:hover {
      background: #2980b9;
    }
  </style>
</head>
<body>
  <h2 style="text-align:center;">Logs</h2>
  <table>
    <tr>
      <th>ID</th>
      <th>User ID</th>
      <th>User IP</th>
      <th>Source</th>
      <th>State</th>
      <th>Error</th>
      <th>Time</th>
      <th>View</th>
    </tr>
    <?php foreach ($logs as $log): ?>
    <tr>
      <td><?= $log->id ?></td>
      <td><?= $log->user_id ?></td>
      <td><?= $log->user_ip ?></td>
      <td><?= $log->permission_source ?></td>
      <td><?= $log->permission_state ?></td>
      <td><?= $log->error_message ?></td>
      <td><?= $log->created_at ?></td>
      <td><a href="<?= base_url('Menu/GetuserLogsliveDetailsData/' . $log->id) ?>"><button class="view-btn">View</button></a></td>
    </tr>
    <?php endforeach; ?>
  </table>
</body>
</html>
