<?php
  session_start();
  require('../app/class/Config.inc.php');

  if(isset($_GET['logout']) && $_GET['logout']==1): 
    $_SESSION = array();
    session_destroy();
    header('Location: ../index.php');
  endif;

  $_SESSION['user_logged'] = $_SESSION['user_logged'] ?? false;
  if($_SESSION['user_logged']!=3) die("Página retrista volte e faca o login no sistema!!!");
  




  
  
  $corJanelaMensagem = 'bg-branco';
  $txCor = 'tx-azul';

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Encantos Finanças</title>
    <!-- Bootstrap core CSS -->
    <link href="../app/assets/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="../app/css/offcanvas.css" rel="stylesheet">

  </head>

  <body class="bg-body">
    
<nav class="navbar navbar-expand-sm fixed-top navbar-dark bg-dark container-menu">
<?php echo baseMenu();?>
  <button class="navbar-toggler p-0 border-0" type="button" data-toggle="offcanvas">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="navbar-collapse offcanvas-collapse" id="navbarsExampleDefault">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item"><a class="nav-link" href="cadastro-de-notas.php">Cadastro de Notas</a></li>
      <li class="nav-item"><a class="nav-link" href="cadastro-de-itens.php">Cadastro de itens</a></li>
      <li class="nav-item active"><a class="nav-link" href="relatorios.php">Relatorio</a></li>
      <li class="nav-item"><a class="nav-link" href="notas.php">Notas</a></li>
    </ul>
    <form class="form-inline my-2 my-lg-0" method="get">
      <input class="form-control mr-sm-2" type="text" placeholder="N° NOTA" name="idnota">
      <button class="btn btn-outline-success my-2 my-sm-0" type="submit" name="pesquisa" value="envia"><?php echo getSvg('search', '20', 'tx-branco');?></button>
    </form>
  </div>

</nav>

<main role="main" class="container">
  
  <div class="alert <?php echo $corJanelaMensagem;?> mt-3 shadow-sm" role="alert">
    <div class="d-flex">
      <div class="p-4 flex-shrink-1">
        <?php echo getSvg('exclamation-circle', '63', $txCor); ?>
      </div>
      <div class="p-2 w-100">
        <h5 class="alert-heading <?php echo $txCor; ?>">Relatórios</h5>
        <hr>
        <p class="<?php echo $txCor; ?>">This example text is going to run a bit longer so that you can see how spacing within an alert works with this kind of content.</p>
      </div>
    </div>
  </div>

  <div class="my-3 p-3 bg-white rounded shadow-sm">
    <h6 class="border-bottom border-gray pb-2 mb-0">Recent updates</h6>
    
    <div class="media text-muted pt-3">
      <p class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
        <strong class="d-block text-gray-dark">@username</strong>
        Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.
      </p>
    </div>

    <small class="d-block text-right mt-3">
      <a href="#">Aroni Souza</a>
    </small>
  </div>

</main>

<script src="../app/java/jquery-3.5.1.slim.min.js"></script>
<script src="../app/assets/dist/js/bootstrap.bundle.min.js"></script>
<script src="../app/java/offcanvas.js"></script>

<script>
  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  });

</script>
</body>
</html>
