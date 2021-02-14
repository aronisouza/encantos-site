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
  $txCor = 'tx-preto';
  $totalEquipamentos=null;

  $AroniSouza = '<small class="d-block text-right mt-3"><a href=\"#">@ Aroni Souza</a></small>';

  // Receitas
  $receita = new Read;
  $receita->ExeRead('receita');
  
  // Itens de receita
  $itensComprados = new Read;
  $itensComprados->ExeRead('notas_itens');
  if($itensComprados->getRowCount()):
    
    // lista os Equipamentos
    for ($i=0; $i < $itensComprados->getRowCount(); $i++):
      if($itensComprados->getResult()[$i]['categoria']=='Equipamento'):
        $valori=0;
        if($itensComprados->getResult()[$i]['quantidade']>1):
          $valori = '<strong>Total:</strong> '.formatoDinheiro($itensComprados->getResult()[$i]['valor']).' <strong>por unidade:</strong> '.formatoDinheiro($itensComprados->getResult()[$i]['valor']/$itensComprados->getResult()[$i]['quantidade']);
        else:
          $valori = '<strong>'.formatoDinheiro($itensComprados->getResult()[$i]['valor']).'</strong>';
        endif;
        $totalEquipamentos = $totalEquipamentos.'<p>'.$itensComprados->getResult()[$i]['quantidade'].' - '.$itensComprados->getResult()[$i]['produto'].', com valor: '.$valori.'</p>';
      endif;
    endfor;


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
        <img src="img-sabonetes/Sabonete-de-Salruda.jpg" class="img-receita">
      </div>

      <div class="p-2 mt-3">
        <h5 class="alert-heading <?php echo $txCor;?>">TEXTO CABEÇALHO</h5>
        <hr>
        <div class="row">
          <nav class="nav">
            <a class="nav-link" data-toggle="collapse" href="#total-tudo" role="button" aria-expanded="false" aria-controls="total-tudo">TOTAL TUDO</a><?php echo getSvg('chevron-compact-right', '20', 'tx-preto mt-2');?>
            <a class="nav-link" data-toggle="collapse" href="#total-embalagem" role="button" aria-expanded="false" aria-controls="total-embalagem">TOTAL EMBALAGEM</a><?php echo getSvg('chevron-compact-right', '20', 'tx-preto mt-2');?>
            <a class="nav-link" data-toggle="collapse" href="#total-equipamento" role="button" aria-expanded="false" aria-controls="total-equipamento">TOTAL EQUIPAMENTO</a><?php echo getSvg('chevron-compact-right', '20', 'tx-preto mt-2');?>
            <a class="nav-link" data-toggle="collapse" href="#total-material" role="button" aria-expanded="false" aria-controls="total-material">TOTAL MATERIAL</a>
          </nav>
        </div>
      </div>
    </div>

    <div class="my-3 p-3 bg-branco rounded shadow-sm collapse" id="total-tudo">
      <h6 class="border-bottom border-gray pb-2 mb-0">RELATÓRIO :: <?php echo getSvg('tools', '20', 'tx-preto');?> Equipamento adquiridos até o momento</h6>
      <div class="card-f card-body-f mt-4">
        <?php echo 'Tudo';?>
      </div>
      <?php echo $AroniSouza;?>
    </div>

    <div class="my-3 p-3 bg-branco rounded shadow-sm collapse" id="total-embalagem">
      <h6 class="border-bottom border-gray pb-2 mb-0">RELATÓRIO :: <?php echo getSvg('tools', '20', 'tx-preto');?> Equipamento adquiridos até o momento</h6>
      <div class="card-f card-body-f mt-4">
        <?php echo 'Embalagem';?>
      </div>
    </div>

    <div class="my-3 p-3 bg-branco rounded shadow-sm collapse" id="total-equipamento">
      <h6 class="border-bottom border-gray pb-2 mb-0">RELATÓRIO :: <?php echo getSvg('tools', '20', 'tx-preto ml-3 mr-3');?> Equipamento adquiridos até o momento</h6>
      <div class="card-f card-body-f mt-4">
        <?php echo $totalEquipamentos;?>
      </div>
      <?php echo $AroniSouza;?>
    </div>

    <div class="my-3 p-3 bg-branco rounded shadow-sm collapse" id="total-material">
      <h6 class="border-bottom border-gray pb-2 mb-0">RELATÓRIO :: <?php echo getSvg('tools', '20', 'tx-preto');?> Equipamento adquiridos até o momento</h6>
      <div class="card-f card-body-f mt-4">
        <?php echo 'Material';?>
      </div>
      <?php echo $AroniSouza;?>
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
