<?php
  session_start();
  
  require('../app/class/Config.inc.php');

  $login = new Login(3);
  
  if (!$login->CheckLogin()):
		unset($_SESSION['userlogin']);
		header('Location: http://$_SERVER[HTTP_HOST]/encantosdoflorescer/index.php');
	else :
		$userlogin = $_SESSION['userlogin'];
	endif;

  $corJanelaMensagem='bg-branco';
  $textoJanela='Mensagem padrão';
  $jnSVG='flag';
  $txCor='tx-azul';
  $select=null;

  $GerarCombo = new Read;
  $GerarCombo->ExeRead('receita');
  if($GerarCombo->getRowCount()):
    for ($i=0; $i < $GerarCombo->getRowCount(); $i++) { 
      $select = $select."<option value=\"{$GerarCombo->getResult()[$i]['id']}\">{$GerarCombo->getResult()[$i]['nome']}</option>";
    }
  endif;

  if(isset($_GET['retorno']) && $_GET['retorno']=='Ok'):
    $corJanelaMensagem='bg-azul';
    $textoJanela='Produto lançado com sucesso!!!';
    $jnSVG='emoji-smile';
    $txCor='tx-branco';
  elseif(isset($_GET['retorno']) && $_GET['retorno']=='Nok'):
    $corJanelaMensagem='bg-vermelho';
    $textoJanela='Não foi possivel realizar o lançamento...';
    $jnSVG='emoji-frown';
    $txCor='tx-preto';
  endif;

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Encantos Estoque</title>
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
        <li class="nav-item"><a class="nav-link active">Lançar Produção</a></li>
        <li class="nav-item"><a class="nav-link" href="transferir-saida.php">Transferir para SAIDA</a></li>
        <li class="nav-item"><a class="nav-link" href="informar-venda.php">Informar VENDA</a></li>
        <li class="nav-item"><a class="nav-link" href="fiado.php">Fiado</a></li>
     </ul>
    </div>
  </nav>
  <main role="main" class="container">
    
    <div class="alert <?php echo $corJanelaMensagem;?> mt-3 shadow-sm" role="alert">
      <div class="d-flex">
        <div class="p-4 flex-shrink-1">
          <?php echo getSvg($jnSVG, '63', $txCor); ?>
        </div>
        <div class="p-2 w-100">
          <h5 class="alert-heading <?php echo $txCor; ?>">Lançamento de Produção</h5>
          <hr>
          <p class="<?php echo $txCor; ?>"><?php echo $textoJanela;?></p>
        </div>
      </div>
    </div>

    <div class="my-3 p-3 bg-branco rounded shadow-sm">
      <h6 class="border-bottom border-gray pb-2 mb-0">Estoque</h6>
      
      <form class="mt-4" id="formid" method="post" action="function/create.php" enctype='multipart/form-data'>

        <div class="form-row">
          
          <div class="form-group col-md-6">
            <label for="produto_nome">Produto</label>
            <select id="produto_nome" name="produto_nome" class="form-control hover">
              <?php echo $select;?>
            </select>
          </div>

          <div class="form-group col-md-3">
            <label for="data_producao">Data de Produção</label>
            <input type="date" class="form-control" id="data_producao" name="data_producao">
          </div>

          <div class="form-group col-md-3">
            <label for="quantidade">Quantidade Produzido</label>
            <input type="number" class="form-control" id="quantidade" min="0" max="100" name="quantidade">
          </div>

          <div class="form-group form-check mt-45">
            <input type="checkbox" class="form-check-input" id="alteracao" name="alteracao">
            <label class="form-check-label" for="alteracao">Alteração no Volume</label>
          </div>
          <div class="form-group col-md-3 ml-2" id="volume_novo-h">
            <label for="volume_novo">Informar alteração</label>
            <input type="text" class="form-control" id="volume_novo" name="volume_novo">
          </div>
          
          <div class="col-lg-12" style="text-align: right;">
            <button class="btn btn-outline-info" type="submit" value="produto" name="lacarProducao">Lançar Produção</button>
          </div>
        </div>

      </form>

      <small class="d-block text-right mt-3">
        <a href="#">@ Aroni Souza</a>
      </small>
    </div>
  </main>

<script src="../app/java/jquery-3.5.1.slim.min.js"></script>
<script src="../app/assets/dist/js/bootstrap.bundle.min.js"></script>
<script src="../app/java/offcanvas.js"></script>
<script src="../app/java/jquery-maskmoney.js"></script>
<script>
  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  });

  $(document).ready(function()
  {
    
    $("#volume_novo").maskMoney({
        prefix: "& ",
        decimal: ".",
        precision: 3,
        thousands: ","
    });
    $("#volume_novo-h").hide();
  });

  $('#alteracao').click(function() {
    $("#volume_novo-h").toggle(this.checked);
  });

  $('#formid').on('keyup keypress', function(e) {
  var keyCode = e.keyCode || e.which;
    if (keyCode === 13) { 
      e.preventDefault();
      return false;
    }
  });
</script>
</body>
</html>
