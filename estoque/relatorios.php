<?php
  require('../app/class/Config.inc.php');
  
  $tabela = $_GET['tabela'];
  $op = isset($_GET['op'])?$_GET['op']:null;
  $m = date('m'); // mês atual`data_venda`
  $y = date('Y'); // ano atual`data_venda`

  $tbTh ='';
  $tbThVE ='';

  $producaoTotal ='';
  $producaoTotalM ='';
  $retornoTabela ='';
  
  $TabelExtenso = ['producao'=> ' de Produção', 'saidas'=> ' de Saidas para Revenda', 'vendas'=> " de {$op}"];

  if($tabela=='producao'):
    // Entrada total por unidade
    $lerPro = new Read;
    $lerPro->FullRead("SELECT SUM(`qnt_prod`) AS `total` FROM `{$tabela}`");
    $producaoTotal = $lerPro->getResult()[0]['total'];
    
    // Entrada total por mes [Day, Month, Year]
    $lerProM = new Read;
    $lerProM->FullRead("SELECT SUM(`qnt_prod`) AS `totalM` FROM `{$tabela}` WHERE MONTH(`data_producao`) = {$m}");
    $producaoTotalM = $lerProM->getResult()[0]['totalM']==0?0:$lerProM->getResult()[0]['totalM'];

    // Quantidade por produto
    // - nome produto | total | total mes | total estoque
    $lProduto = new Read;
    $lProduto->ExeRead('receita');
    if($lProduto->getResult()):
      // vai ate tabela producao e faz oq tem q ser feito
      // Calcula total produzido de cada produto
      foreach ($lProduto->getResult() as $key => $value) {
        $producaoTT = new Read;
        $producaoTT->FullRead("SELECT SUM(`qnt_prod`) AS `total` FROM `{$tabela}` WHERE `produto_id`= {$value['id']}");
        $producaoTM = new Read;
        $producaoTM->FullRead("SELECT SUM(`qnt_prod`) AS `totalM` FROM `{$tabela}` WHERE MONTH(`data_producao`) = {$m} AND `produto_id`= {$value['id']}");
        $producaoTE = new Read;
        $producaoTE->FullRead("SELECT SUM(`quantidade`) AS `totalE` FROM `{$tabela}` WHERE `produto_id`= {$value['id']}");
        
        $mesR = $producaoTM->getResult()[0]['totalM'] ==0?0:$producaoTM->getResult()[0]['totalM'];

        $tbTh = $tbTh."
          <tr>
            <td>{$value['nome']}</td>
            <td>{$producaoTT->getResult()[0]['total']}</td>
            <td>{$mesR}</td>
            <td>{$producaoTE->getResult()[0]['totalE']}</td>
          </tr>
        ";
      }

      $retornoTabela = "
        <table class=\"table table-bordered mt-3 table-hover\">
          <thead>
            <tr>
              <th scope=\"col\">Produto</th>
              <th scope=\"col\">Total Geral</th>
              <th scope=\"col\">Total Mês</th>
              <th scope=\"col\">Em Estoque</th>
            </tr>
          </thead>
          <tbody>
            {$tbTh}
          </tbody>
        </table>
        ";
    endif;
  endif;

  if($tabela=='saidas'):
    // Entrada total por unidade
    $lerPro = new Read;
    $lerPro->FullRead("SELECT SUM(`quantidade`) AS `total` FROM `{$tabela}`");
    $producaoTotal = $lerPro->getResult()[0]['total'];
    // Entrada total por mes [Day, Month, Year]
    $lerProM = new Read;
    $lerProM->FullRead("SELECT SUM(`quantidade`) AS `totalM` FROM `{$tabela}` WHERE MONTH(`data_producao`) = {$m}");
    $producaoTotalM = $lerProM->getResult()[0]['totalM']==0?0:$lerProM->getResult()[0]['totalM'];
    // Quantidade por produto
    // - nome produto | total | total mes | total estoque
    $lProduto = new Read;
    $lProduto->ExeRead('receita');
    if($lProduto->getResult()):
      // vai ate tabela producao e faz oq tem q ser feito
      // Calcula total produzido de cada produto
      foreach ($lProduto->getResult() as $key => $value) {
        $producaoTT = new Read;
        $producaoTT->FullRead("SELECT SUM(`quantidade`) AS `total` FROM `{$tabela}` WHERE `produto_id`= {$value['id']}");
        $producaoTM = new Read;
        $producaoTM->FullRead("SELECT SUM(`quantidade`) AS `totalM` FROM `{$tabela}` WHERE MONTH(`data_producao`) = {$m} AND `produto_id`= {$value['id']}");
        $mesR = $producaoTM->getResult()[0]['totalM'] ==0?0:$producaoTM->getResult()[0]['totalM'];
        $tbTh = $tbTh."
          <tr>
            <td>{$value['nome']}</td>
            <td>{$producaoTT->getResult()[0]['total']}</td>
            <td>{$mesR}</td>
          </tr>
        ";
      }

      $lRevendedor = new Read;
      $lRevendedor->ExeRead('revendedor');
      foreach ($lRevendedor->getResult() as $key => $value) {
        $producaoTTe = new Read;
        $producaoTTe->FullRead("SELECT SUM(`quantidade`) AS `total` FROM `{$tabela}` WHERE `destino_apelido`='{$value['apelido']}'");
        $producaoTMe = new Read;
        $producaoTMe->FullRead("SELECT SUM(`quantidade`) AS `totalM` FROM `{$tabela}` WHERE MONTH(`data_producao`) = {$m} AND `destino_apelido`='{$value['apelido']}'");
        $mesRe = $producaoTMe->getResult()[0]['totalM'] ==0?0:$producaoTMe->getResult()[0]['totalM'];
        $tbThVE = $tbThVE."
          <tr>
            <td>{$value['apelido']}</td>
            <td>{$producaoTTe->getResult()[0]['total']}</td>
            <td>{$mesRe}</td>
          </tr>
        ";
      }

      $retornoTabela = "
        <div class=\"row\">
          <div class=\"col\">
            <table class=\"table table-bordered mt-3 table-hover\">
              <thead>
                <tr>
                  <th scope=\"col\">Produto</th>
                  <th scope=\"col\">Total Geral</th>
                  <th scope=\"col\">Total Mês</th>
                </tr>
              </thead>
              <tbody>
                {$tbTh}
              </tbody>
            </table>
          </div>
          <div class=\"col\">
            <table class=\"table table-bordered mt-3 table-hover\">
              <thead>
                <tr>
                  <th scope=\"col\">Vendedor</th>
                  <th scope=\"col\">Total Geral</th>
                  <th scope=\"col\">Total Mês</th>
                </tr>
              </thead>
              <tbody>
                {$tbThVE}
              </tbody>
            </table>
          </div>
        </div>
      ";
    endif;
  endif;

  if($tabela=='vendas'):
    // Entrada total por unidade
    $lerPro = new Read;
    $tipoPes = $op=='quebras' ? "`forma_pagamento`='Quebras'": "`forma_pagamento`!='Quebras'";
    $lerPro->FullRead("SELECT SUM(`quantidade`) AS `total` FROM `{$tabela}` WHERE {$tipoPes}");
    $producaoTotal = $lerPro->getResult()[0]['total']==0?0:$lerPro->getResult()[0]['total'];

    // Entrada total por mes [Day, Month, Year]
    $lerProM = new Read;
    $lerProM->FullRead("SELECT SUM(`quantidade`) AS `totalM` FROM `{$tabela}` WHERE YEAR(`data_venda`) = {$y} AND MONTH(`data_venda`) = {$m}");

	
    $producaoTotalM = $lerProM->getResult()[0]['totalM']==0?0:$lerProM->getResult()[0]['totalM'];

    // Quantidade por produto
    // - nome produto | total | total mes | total estoque
    $lProduto = new Read;
    $lProduto->ExeRead('receita');
    if($lProduto->getResult()):
      // vai ate tabela producao e faz oq tem q ser feito
      // Calcula total produzido de cada produto
      foreach ($lProduto->getResult() as $key => $value) {
        $tipoPes = $op=='quebras' ? "AND `forma_pagamento`='Quebras'": "AND `forma_pagamento`!= 'Quebras'";
        $producaoTT = new Read;
        $producaoTT->FullRead("SELECT SUM(`quantidade`) AS `total`  FROM `{$tabela}` WHERE `produto_id`= {$value['id']} {$tipoPes}");
        $producaoTM = new Read;
        $producaoTM->FullRead("SELECT SUM(`quantidade`) AS `totalM` FROM `{$tabela}` WHERE `produto_id`= {$value['id']} {$tipoPes} AND YEAR(`data_venda`) = {$y} AND MONTH(`data_venda`) = {$m}");
        $pTT  = $producaoTT->getResult()[0]['total']  ==0? 0: $producaoTT->getResult()[0]['total'];
        $mesR = $producaoTM->getResult()[0]['totalM'] ==0? 0: $producaoTM->getResult()[0]['totalM'];
        $tbTh = $tbTh."
          <tr>
            <td>{$value['nome']}</td>
            <td>{$pTT}</td>
            <td>{$mesR}</td>
          </tr>
        ";
      }

      if($op=='quebras'):
        $ValorPES = ['Lixo','Uso','Doação'];
        foreach ($ValorPES as $value) {
          $producaoTT = new Read;
          $producaoTT->FullRead("SELECT SUM(`quantidade`) AS `total` FROM `{$tabela}` WHERE `obs_forma_pagamento` LIKE '%{$value}%' AND `forma_pagamento`='Quebras'");
          $pTT = $producaoTT->getResult()[0]['total']  ==0? 0: $producaoTT->getResult()[0]['total'];

          $producaoTM = new Read;
          $producaoTM->FullRead("SELECT SUM(`quantidade`) AS `totalM` FROM `{$tabela}` WHERE MONTH(`data_venda`)=$m AND `forma_pagamento`='Quebras' AND `obs_forma_pagamento` LIKE '%{$value}%'");
          $mesR = $producaoTM->getResult()[0]['totalM'] ==0? 0: $producaoTM->getResult()[0]['totalM'];
          $tbThVE = $tbThVE."
            <tr>
              <td>{$value}</td>
              <td>{$pTT}</td>
              <td>{$mesR}</td>
            </tr>
          ";
        }
      else:
        $lFormaPag = new Read;
        $lFormaPag->FullRead("SELECT SUM(`quantidade`) AS `total` , `forma_pagamento` FROM `{$tabela}` WHERE `forma_pagamento` != 'Quebras'");
        for ($i=0; $i < $lFormaPag->getRowCount(); $i++) { 
          if($lFormaPag->getResult()[$i]['forma_pagamento']!='Quebras'):
            $pTTf = $lFormaPag->getResult()[0]['total']  ==0? 0: $lFormaPag->getResult()[0]['total'];
          endif;
        }
        $lFormaPag->FullRead("SELECT SUM(`quantidade`) AS `totalM`,  `forma_pagamento` FROM `{$tabela}` WHERE MONTH(`data_venda`)=$m  AND `forma_pagamento` != 'Quebras'");
        for ($i=0; $i < $lFormaPag->getRowCount(); $i++) { 
          if($lFormaPag->getResult()[$i]['forma_pagamento']!='Quebras'):
              $mesRf = $lFormaPag->getResult()[0]['totalM'] ==0? 0: $lFormaPag->getResult()[0]['totalM'];
          endif;
        }
        $tbThVE = $tbThVE."
          <tr>
            <td>Venda Realizada</td>
            <td>{$pTTf}</td>
            <td>{$mesRf}</td>
          </tr>
        ";
      endif;
      $opTab = $op=='quebras'?'Quebras':'Vendas';
      $retornoTabela = "
        <div class=\"row\">
          <div class=\"col\">
            <table class=\"table table-bordered mt-3 table-hover\">
              <thead>
                <tr>
                  <th scope=\"col\">Produto</th>
                  <th scope=\"col\">Total Geral</th>
                  <th scope=\"col\">Total Mês</th>
                </tr>
              </thead>
              <tbody>
                {$tbTh}
              </tbody>
            </table>
          </div>
          <div class=\"col\">
            <table class=\"table table-bordered mt-3 table-hover\">
              <thead>
                <tr>
                  <th scope=\"col\">{$opTab}</th>
                  <th scope=\"col\">Total Geral</th>
                  <th scope=\"col\">Total Mês</th>
                </tr>
              </thead>
              <tbody>
                {$tbThVE}
              </tbody>
            </table>
          </div>
        </div>
      ";
    endif;
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
        <li class="nav-item"><a class="nav-link" href="lancamento-producao.php">Lançar Produção</a></li>
        <li class="nav-item"><a class="nav-link" href="transferir-saida.php">Transferir para SAIDA</a></li>
        <li class="nav-item"><a class="nav-link" href="informar-venda.php">Informar VENDA</a></li>
        <li class="nav-item"><a class="nav-link" href="fiado.php">Fiado</a></li>
      </ul>
      
    </div>
  </nav>
  <main role="main" class="container">
    
    <div class="my-3 p-3 bg-branco rounded shadow-sm">
      
      <h6 class="border-bottom border-gray pb-2 mb-0">
        <?php echo getSvg('journal-text', '28', 'tx-azul mr-3');?> Relatórios<?php echo $TabelExtenso[$tabela]; ?></h6>

        <div class="media text-muted pt-3">
          
          <div class="card wh100">
            <h5 class="card-header bg-azul text-center tx-preto">Quantidade Total <?php echo $TabelExtenso[$tabela];?></h5>
            <div class="card-body">
              <p class="card-text text-center"><?php echo $producaoTotal;?></p>
            </div>
          </div>

          <div class="card ml-3 wh100">
            <h5 class="card-header bg-amarelo text-center tx-preto">Total <?php echo $TabelExtenso[$tabela]; ?> no Mês</h5>
            <div class="card-body">
              <p class="card-text text-center"><?php echo $producaoTotalM;?></p>
            </div>
          </div>

        </div>
      
      
      
      <?php echo $retornoTabela;?>
    
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
