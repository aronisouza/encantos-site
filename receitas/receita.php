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

  $mensagem = null;
  $corJanelaMensagem = 'bg-branco';
  $txCor = 'tx-azul';
  $textoJanela='teste receita';
  $jnSVG='emoji-heart-eyes';
  $forItens='';

  $custoArrayE = 0;
  $custoArrayS = 0;

  $getGet = [
    'id_receita'      =>$_GET['idReceita'],
    'nomeReceita'     =>$_GET['nomeReceita'],
    'medida'          =>$_GET['medida'],
    'tipo'            =>$_GET['tipo'],
    'rendimento'      =>$_GET['rendimento'],
    'preco_real'      =>$_GET['precoReal'],
    'imagem'          =>$_GET['imagem']
  ];


  $sabonete = new Read;
  $sabonete->ExeRead('receitas_itens', 'WHERE id_receita=:tabelaId', "tabelaId={$getGet['id_receita']}");
  if($sabonete->getRowCount()):
    for($i = 0 ; $i < $sabonete->getRowCount(); $i++):
      $precoKgLt=($sabonete->getResult()[$i]['preco_pago']/$sabonete->getResult()[$i]['volume']);
      $valorComFrete=(($precoKgLt*$sabonete->getResult()[$i]['frete']) + $precoKgLt);
      $custoArrayS = $custoArrayS + ($valorComFrete*$sabonete->getResult()[$i]['utilizado']);
    endfor;
  endif;

  $embalagem = new Read;
  $embalagem->ExeRead('receitas_embalagem', 'WHERE id_receita=:tabelaId', "tabelaId={$getGet['id_receita']}");
  if($embalagem->getRowCount()):
    for($i = 0 ; $i < $embalagem->getRowCount(); $i++):
      $precoKgLt=($embalagem->getResult()[$i]['preco_pago']/$embalagem->getResult()[$i]['volume']);
      $valorComFrete=(($precoKgLt*$embalagem->getResult()[$i]['frete']) + $precoKgLt);
      $custoArrayE = $custoArrayE + (($valorComFrete*$embalagem->getResult()[$i]['utilizado'])*$getGet['rendimento']);
    endfor;
  endif;

  $lucroRealRec =formatoDinheiro(($getGet['preco_real']*$getGet['rendimento'])-($custoArrayS+$custoArrayE));

  $read = new Read;
  $read->ExeRead('receitas_itens', 'WHERE id_receita=:id', "id={$getGet['id_receita']}");

  if($read->getRowCount()):
    for($i = 0 ; $i < $read->getRowCount(); $i++):
      
      if($read->getResult()[$i]['tipo_volume']=='Gramas'): $udm = 'gr';endif;
      if($read->getResult()[$i]['tipo_volume']=='Mililitros'): $udm = 'ml';endif;
      if($read->getResult()[$i]['tipo_volume']=='Centimetros'): $udm = 'cm';endif;
      if($read->getResult()[$i]['tipo_volume']=='Unidade'): $udm = 'uni';endif;

      $forItens = $forItens. "
        <div class=\"row mt-1\">
          <div class=\"col\">
          • <span class=\"span-ml\">{$read->getResult()[$i]['utilizado']} {$udm}</span> <span class=\"span-ml\">{$read->getResult()[$i]['materia']}</span>
          </div>
        </div>
      ";

    endfor;
  else:
    $mensagem ='Ainda não temos itens para a receitas.';
  endif;

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Encantos Receitas</title>
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
        <li class="nav-item"><a class="nav-link" href="adicionar-receita.php">Adicionar Receita</a></li>
        <li class="nav-item"><a class="nav-link" href="itens-de-receita.php">Itens de uma Receita</a></li>
        <li class="nav-item"><a class="nav-link" href="embalagem-de-receita.php">Embalagem de uma Receita</a></li>
      </ul>
      <form class="form-inline my-2 my-lg-0" method="get">
        <input class="form-control mr-sm-2" type="text" placeholder="Desativado" name="idnota" disabled>
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit" name="pesquisa" value="envia" disabled><?php echo getSvg('search', '20', 'tx-branco');?></button>
      </form>
    </div>
  </nav>
  <main role="main" class="container">
    
    <div class="alert <?php echo $corJanelaMensagem;?> mt-3 shadow-sm" role="alert">
      <div>
        <img src="<?php echo $getGet['imagem'];?>" class="img-receita">
      </div>

      <div class="p-2 mt-3">
        <h5 class="alert-heading <?php echo $txCor;?>">Receita <?php echo $getGet['nomeReceita'];?></h5>
        <hr>

        <div class="row">
          
          <div class="col-sm-3 mb-3">
            <div class="card">
              <div class="card-header text-center">
                <?php echo getSvg('funnel', '40', 'tx-laranja');?>
              </div>
              <ul class="list-group list-group-flush">
                <li class="list-group-item text-center">Rendimento</li>
                <li class="list-group-item text-center"><?php echo $getGet['rendimento'].' '. $getGet['tipo'];?></li>
              </ul>
            </div>
          </div>

          <div class="col-sm-3 mb-3">
            <div class="card">
              <div class="card-header text-center">
                <?php echo getSvg('cart3', '40', 'tx-laranja');?>
              </div>
              <ul class="list-group list-group-flush">
                <li class="list-group-item text-center">Preço de Venda</li>
                <li class="list-group-item text-center"><?php echo formatoDinheiro($getGet['preco_real']);?></li>
              </ul>
            </div>
          </div>

          <div class="col-sm-3 mb-3">
            <div class="card">
              <div class="card-header text-center">
                <?php echo getSvg('bootstrap-reboot', '40', 'tx-laranja');?>
              </div>
              <ul class="list-group list-group-flush">
                <li class="list-group-item text-center">Lucro Real Receita</li>
                <li class="list-group-item text-center"><?php echo formatoDinheiro(($getGet['preco_real']*$getGet['rendimento'])-($custoArrayS+$custoArrayE));?></li>
              </ul>
            </div>
          </div>
          
          <div class="col-sm-3 mb-3">
            <div class="card">
              <div class="card-header text-center">
                <?php echo getSvg('file-binary', '40', 'tx-laranja');?>
              </div>
              <ul class="list-group list-group-flush">
                <li class="list-group-item text-center">Lucro Real Unidade</li>
                <li class="list-group-item text-center"><?php echo formatoDinheiro((($getGet['preco_real']*$getGet['rendimento'])-($custoArrayS+$custoArrayE))/$getGet['rendimento']);?></li>
              </ul>
            </div>
          </div>

        </div>

      </div>
    </div>

    <div class="my-3 p-3 bg-branco rounded shadow-sm">
      <h6 class="border-bottom border-gray pb-2 mb-0"><?php echo getSvg('book-half', '30', 'tx-azul');?> INGREDIENTES</h6>
      

      <div class="container mt-3">
        <?php echo $forItens;?>
      </div>



      <small class="d-block text-right mt-3">
        <a href="#">@ Aroni Souza</a>
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
