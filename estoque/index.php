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
 
  $lerPro = new Read;
  // $lerPro->ExeRead('producao');

  $lerPro->FullRead('SELECT SUM(`qnt_prod`) AS total FROM `producao` ');
  $producaoTotal = $lerPro->getResult()[0]['total'];

  $dadosPro = [
    'id'            => '',
    'produto_id'    => '',
    'produto_nome'  => '',
    'data_producao' => '',
    'data_validade' => '',
    'alteracao'     => '',
    'volume_novo'   => '',
    'quantidade'    => '',
    'qnt-prod'      => ''
  ];

  $lerSai = new Read;
  $lerSai->FullRead('SELECT SUM(`quantidade`) AS total FROM `saidas` ');
  $saidasTotal = $lerSai->getResult()[0]['total'];

  $dadosSai = [
    'id'              => '',
    'produto_id'      => '',
    'produto_nome'    => '',
    'data_producao'   => '',
    'data_validade'   => '',
    'quantidade'      => '',
    'producao_id'     => '',
    'data_saida'      => '',
    'destino_id'      => '',
    'destino_apelido' => ''
  ];

  $lerVen = new Read;
  $lerVen->FullRead("SELECT SUM(`quantidade`) AS total FROM `vendas` WHERE `forma_pagamento`!='Quebras'");
  $vendasTotal = $lerVen->getResult()[0]['total']==0?0:$lerVen->getResult()[0]['total'];

  $lerQuebras = new Read;
  $lerQuebras->FullRead("SELECT SUM(`quantidade`) AS total FROM `vendas` WHERE `forma_pagamento`='Quebras'");
  $quebrasTotal = $lerQuebras->getResult()[0]['total']==0?0:$lerQuebras->getResult()[0]['total'];

  $dadosVen = [
    'id'                  => '',
    'produto_id'          => '',
    'produto_nome'        => '',
    'data_producao'       => '',
    'data_validade'       => '',
    'quantidade'          => '',
    'producao_id'         => '',
    'data_venda'          => '',
    'origem_id'           => '',
    'origem_nome'         => '',
    'valor_unidade'       => '',
    'valor_total'         => '',
    'forma_pagamento'     => '',
    'obs_forma_pagamento' => ''
  ];



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
        <li class="nav-item"><a class="nav-link" href="lancamento-producao.php">Lançar Produção</a></li>
        <li class="nav-item"><a class="nav-link" href="transferir-saida.php">Transferir para SAIDA</a></li>
        <li class="nav-item"><a class="nav-link" href="informar-venda.php">Informar VENDA</a></li>
        <li class="nav-item"><a class="nav-link" href="fiado.php">Fiado</a></li>
      </ul>
      
    </div>
  </nav>
  <main role="main" class="container">
    
    <div class="my-3 p-3 bg-branco rounded shadow-sm">
      <h6 class="border-bottom border-gray pb-2 mb-0">Estoque</h6>
      
      <div class="media text-muted pt-3">
        
        <div class="card wh100">
          <h5 class="card-header bg-azul text-center tx-preto">Entrada</h5>
          <div class="card-body">
            <h5 class="card-text text-center">Quantidade Produzida</h5>
            <p class="card-text text-center"><?php echo $producaoTotal;?></p>
            <a href="relatorios.php?tabela=producao" class="btn btn-outline-primary w100">Mais informações</a>
          </div>
        </div>

        <div class="card ml-3 wh100">
          <h5 class="card-header bg-amarelo text-center tx-preto">Saidas</h5>
          <div class="card-body">
            <h5 class="card-title text-center">Com Revendedores</h5>
            <p class="card-text text-center"><?php echo $saidasTotal;?></p>
            <a href="relatorios.php?tabela=saidas" class="btn btn-outline-primary w100">Mais informações</a>
          </div>
        </div>

        <div class="card ml-3 wh100">
          <h5 class="card-header bg-verde text-center tx-preto">Vendas</h5>
          <div class="card-body">
            <h5 class="card-title text-center">Vendidos Dindin</h5>
            <p class="card-text text-center"><?php echo $vendasTotal;?></p>
            <a href="relatorios.php?tabela=vendas&op=vendas" class="btn btn-outline-primary w100">Mais informações</a>
          </div>
        </div>

        <div class="card ml-3 wh100">
          <h5 class="card-header bg-vermelho text-center tx-preto">Quebras</h5>
          <div class="card-body">
            <h5 class="card-title text-center">Errado/Doado</h5>
            <p class="card-text text-center"><?php echo $quebrasTotal;?></p>
            <a href="relatorios.php?tabela=vendas&op=quebras" class="btn btn-outline-primary w100">Mais informações</a>
          </div>
        </div>

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
