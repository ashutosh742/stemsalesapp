<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
  <h2>User Detail</h2>          

  <table class="table">
    <thead>
      <tr>
      <th>id</th>
        <th>Fullname</th>
        <th>Username</th>
        <th>Department</th>
        <th>Role</th>
        <th>
      </tr>
    </thead>
    <tbody>
      <?php
      foreach($data as $d){
          ?>
         <tr>
            <td><?=$d->id?></td>
            <td><?=$d->name?></td>
            <th><?=$d->username?></th>
            <th><?=$d->type_id?></th>
            <th><?=$d->user_id?></th>
            <td><a href="<?=base_url()?>Menu/delete_user?id=<?=$d->id?>">Delete</a></td>
         </tr>
          
          <?php
      }
      ?>
    </tbody>
  </table>

  <a href="<?=base_url()?>Menu/add_users">Add User</a>
</div>

</body>
</html>
