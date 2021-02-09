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

  $_producao = [
    'id' => 0,
    'produto_id' => 0,
    'produto_nome' => 0,
    'data_producao' => 0,
    'data_validade' => 0,
    'alteracao' => 0,
    'volume_novo' => 0,
    'quantidade' => 0,
    'qnt_prod' => 0
  ];

  if(isset($_GET['link'])):
    $link = $_GET['link'];

    // PRODUÇÃO
    $lerLink = new Read;
    $lerLink->FullRead("SELECT * FROM `producao` WHERE `id`='{$link}'");
    $_producao['id'] = $lerLink->getResult()[0]['id'];
    $_producao['produto_id'] = $lerLink->getResult()[0]['produto_id'];
    $_producao['produto_nome'] = $lerLink->getResult()[0]['produto_nome'];
    $_producao['data_producao'] = $lerLink->getResult()[0]['data_producao'];
    $_producao['data_validade'] = $lerLink->getResult()[0]['data_validade'];
    $_producao['alteracao'] = $lerLink->getResult()[0]['alteracao'];
    $_producao['volume_novo'] = $lerLink->getResult()[0]['volume_novo'];
    $_producao['quantidade'] = $lerLink->getResult()[0]['quantidade'];
    $_producao['qnt_prod'] = $lerLink->getResult()[0]['qnt_prod'];

    // SAIDAS
    $lersaida = new Read;
    $lersaida->FullRead("SELECT * FROM `saidas` WHERE `producao_id`='{$link}'");

    // VENDAS
    $lervendas = new Read;
    $lervendas->FullRead("SELECT * FROM `vendas` WHERE `producao_id`='{$link}'");

endif;
   
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="../app/assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Encantos Estoque Relatórios </title>
  </head>
<body>

  <div class="container">
    
    <div class="row mt-3 mb-1">Encantos do Florescer Relatório de produção</div>
    <div class="row border-bottom mb-2">Gestão do Produto - <?php echo $_producao['produto_nome']." | Data Produção: ". $_producao['data_producao']." | Validade até: ".$_producao['data_validade'];?></div>

    <div class="row border-bottom mt-4">
      Dados de Produção
    </div>

    <div class="table-responsive mt-2">
      <table class="table table-sm table-borderless">
        <thead>
          <tr align="center">
            <th scope="col">#</th>
            <th>Nome</th>
            <th>Data de Produção</th>
            <th>Validade</th>
            <th>Alteração</th>
            <th>Novo Volume</th>
            <th>Quantidade Estoque</th>
            <th>Quantidade Produzida</th>
          </tr>
        </thead>
        <tbody>
          <tr align="center">
            <th scope="row"><?php echo $_producao['produto_id'];?></th>
            <td><?php echo $_producao['produto_nome'];?></td>
            <td><?php echo $_producao['data_producao'];?></td>
            <td><?php echo $_producao['data_validade'];?></td>
            <td><?php echo $_producao['alteracao'];?></td>
            <td><?php echo $_producao['volume_novo'];?></td>
            <td><?php echo $_producao['quantidade'];?></td>
            <td><?php echo $_producao['qnt_prod'];?></td>
          </tr>
        </tbody>
      </table>
    </div>
  
    <div class="row border-bottom mt-4">
      Dados de Produtos em Vendedores
    </div>

    <div class="table-responsive mt-2">
      <table class="table table-sm table-borderless">
        <thead>
          <tr align="center">
            <th scope="col">#</th>
            <th>Nome</th>
            <th>Data Produção</th>
            <th>Validade</th>
            <th>Id Produção</th>
            <th>Qnt</th>
            <th>Data Saida</th>
            <th>Enviado para</th>
          </tr>
        </thead>
        <tbody>
          <?php 
            
            for($i = 0 ; $i < $lersaida->getRowCount(); $i++):
              echo "<tr align=\"center\">
                <th scope=\"row\">{$i}</th>
                  <td>{$lersaida->getResult()[$i]['produto_nome']}</td>
                  <td>{$lersaida->getResult()[$i]['data_producao']}</td>
                  <td>{$lersaida->getResult()[$i]['data_validade']}</td>
                  <td>{$lersaida->getResult()[$i]['producao_id']}</td>
                  <td>{$lersaida->getResult()[$i]['quantidade']}</td>
                  <td>{$lersaida->getResult()[$i]['data_saida']}</td>
                  <td>{$lersaida->getResult()[$i]['destino_apelido']}</td>
                  
              </tr>";
            endfor;
          ?>
        </tbody>
      </table>
    </div>

    <div class="row border-bottom mt-4">
      Dados de Produtos Vendidos / Quebras / Outros
    </div>

    <div class="table-responsive mt-2">
      <table class="table table-sm table-borderless">
        <thead>
          <tr align="center">
            <th scope="col">#</th>
            <th>Nome</th>
            <th>Data Produção</th>
            <th>Qnt</th>
            <th>Data de Venda</th>
            <th>Vendedor</th>
            <th>Enviado Vendedor</th>
            <th>Valor Total</th>
            <th>Forma de Pgto</th>
            <th>Obs: Pgto</th>
            
          </tr>
        </thead>
        <tbody>
          <?php 
            
            for($i = 0 ; $i < $lervendas->getRowCount(); $i++):
              echo "<tr align=\"center\">
                <th scope=\"row\">{$i}</th>
                  <td>{$lervendas->getResult()[$i]['produto_nome']}</td>
                  <td>{$lervendas->getResult()[$i]['data_producao']}</td>
                  <td>{$lervendas->getResult()[$i]['quantidade']}</td>
                  <td>{$lervendas->getResult()[$i]['data_venda']}</td>
                  <td>{$lervendas->getResult()[$i]['origem_apelido']}</td>
                  <td>{$lervendas->getResult()[$i]['origem_data_saida']}</td>
                  <td>{$lervendas->getResult()[$i]['valor_total']}</td>
                  <td>{$lervendas->getResult()[$i]['forma_pagamento']}</td>
                  <td>{$lervendas->getResult()[$i]['obs_forma_pagamento']}</td>
                  
              </tr>";
            endfor;
          ?>
        </tbody>
      </table>
    </div>
  </div>
  
</body>
</html>