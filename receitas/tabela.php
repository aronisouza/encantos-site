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

  $tabelaSabonete=null;
  $tabelaEmbalagem=null;
  $custoArrayS=0;
  $custoArrayE=0;
  $rendimento = $_GET['rendimento'];
  $ReceitaNome= $_GET['receitanome'];
  $precoReal= $_GET['precoReal'];

  if (isset($_GET['tabelaId'])):
    $sabonete = new Read;
    $sabonete->ExeRead('receitas_itens', 'WHERE id_receita=:tabelaId', "tabelaId={$_GET['tabelaId']}");
    if($sabonete->getRowCount()):
      for($i = 0 ; $i < $sabonete->getRowCount(); $i++):
        $precoKgLt=($sabonete->getResult()[$i]['preco_pago']/$sabonete->getResult()[$i]['volume']);
        $valorComFrete=(($precoKgLt*$sabonete->getResult()[$i]['frete']) + $precoKgLt);
        $custo = ($valorComFrete*$sabonete->getResult()[$i]['utilizado']);
        $p1 = formatoDinheiro($precoKgLt);
        $p2 = formatoDinheiro($valorComFrete);
        $p3 = formatoDinheiro($custo);
        $p4 = formatoDinheiro($sabonete->getResult()[$i]['preco_pago']);
        $p5 = getPorcerto($sabonete->getResult()[$i]['frete']);
        //cria array com valores
        $custoArrayS = $custoArrayS + $custo;
        // cria linha da tabela
        $tabelaSabonete = $tabelaSabonete ."
          <tr>
            <th>{$sabonete->getResult()[$i]['materia']}</th>
            <td>{$sabonete->getResult()[$i]['volume']}</td>
            <td>{$p4}</td>
            <td>{$p1}</td>
            <td>{$p5}</td>
            <td>{$p2}</td>
            <td>{$sabonete->getResult()[$i]['utilizado']}</td>
            <td>{$p3}</td>
          </tr>";
      endfor;
    endif;
    
    $embalagem = new Read;
    $embalagem->ExeRead('receitas_embalagem', 'WHERE id_receita=:tabelaId', "tabelaId={$_GET['tabelaId']}");
    if($embalagem->getRowCount()):
      for($i = 0 ; $i < $embalagem->getRowCount(); $i++):
        $precoKgLt=($embalagem->getResult()[$i]['preco_pago']/$embalagem->getResult()[$i]['volume']);
        $valorComFrete=(($precoKgLt*$embalagem->getResult()[$i]['frete']) + $precoKgLt);
        $custo2 = ($valorComFrete*$embalagem->getResult()[$i]['utilizado']);
        $p1 = formatoDinheiro($precoKgLt);
        $p2 = formatoDinheiro($valorComFrete);
        $p3 = formatoDinheiro($custo2*$rendimento);
        $p4 = formatoDinheiro($embalagem->getResult()[$i]['preco_pago']);
        $p5 = getPorcerto($embalagem->getResult()[$i]['frete']);
        //cria array com valores
        $custoArrayE = $custoArrayE + ($custo2*$rendimento);
        // cria linha da tabela
        $tabelaEmbalagem = $tabelaEmbalagem ."
          <tr>
            <th>{$embalagem->getResult()[$i]['materia']}</th>
            <td>{$embalagem->getResult()[$i]['volume']}</td>
            <td>{$p4}</td>
            <td>{$p1}</td>
            <td>{$p5}</td>
            <td>{$p2}</td>
            <td>{$embalagem->getResult()[$i]['utilizado']}</td>
            <td>{$p3}</td>
          </tr>";
      endfor;
    endif;
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


  <nav class="navbar navbar-expand-sm navbar-dark bg-dark fixed-top container-menu">
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
        <input class="form-control mr-sm-2" type="text" placeholder="Num funfa" name="idnota" disabled>
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit" name="pesquisa" value="envia" disabled><?php echo getSvg('search', '20', 'tx-branco');?></button>
      </form>
    </div>
  </nav>


  <main role="main" class="container">
    <div class="my-3 p-3 bg-branco rounded shadow-sm">
      <h4 class="border-bottom border-gray pb-2 mb-0">Informações referente a Receita <?php echo $ReceitaNome;?></h4>
      <div class="alert alert-info mt-3" role="alert">
        Esta Receita Rende: <strong><?php echo $rendimento;?></strong> <?php echo $ReceitaNome;?><br />
        Preço de Venda Sugerido pelo Sistema: <strong><?php $soma2=((($custoArrayS/$rendimento)*4)+(($custoArrayE/$rendimento)*2)); echo formatoDinheiro($soma2);?></strong><br />
        Preço de Venda Cadastrado: <strong><?php echo formatoDinheiro($precoReal);?></strong>

      </div>

      <div class="row">
        <div class="col">
          <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center Cinza">
            <strong>Tabela Sabonetes</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center Vermelho">
              Total Custos
              <span class="badge badge-filid badge-pill"><?php echo formatoDinheiro($custoArrayS);?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center Vermelho">
              Total por Unidade
              <span class="badge badge-filid badge-pill"><?php echo formatoDinheiro($custoArrayS/$rendimento);?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center Azul">
              Sugestão 1
              <span class="badge badge-filid badge-pill"><?php echo formatoDinheiro(($custoArrayS/$rendimento)*4);?></span>
            </li>
          </ul>
        </div>

        <div class="col">
          <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center Cinza">
            <strong>Tabela Embalagens</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center Vermelho">
              Total Custos
              <span class="badge badge-filid badge-pill"><?php echo formatoDinheiro($custoArrayE);?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center Vermelho">
              Total por Unidade
              <span class="badge badge-filid badge-pill"><?php echo formatoDinheiro($custoArrayE/$rendimento);?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center Azul">
              Sugestão 2
              <span class="badge badge-filid badge-pill"><?php echo formatoDinheiro(($custoArrayE/$rendimento)*2);?></span>
            </li>
          </ul>
        </div>

        <div class="col">
          <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center Cinza">
            <strong>Total Geral</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center Vermelho">
              Custos
              <span class="badge badge-filid badge-pill"><?php echo formatoDinheiro($custoArrayS+$custoArrayE);?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center Vermelho">
              Custos por Unidade
              <span class="badge badge-filid badge-pill"><?php $soma=(($custoArrayS/$rendimento)+($custoArrayE/$rendimento)); echo formatoDinheiro($soma);?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center Azul">
              Sugestão Final
              <span class="badge badge-filid badge-pill"><?php echo formatoDinheiro($soma2); ?></span>
            </li>
          </ul>
        </div>

        <div class="col">
          <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center Cinza">
            <strong>Definição de Lucro P/Unid.</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center Verde">
              Daniela
              <span class="badge badge-filid badge-pill"><?php echo formatoDinheiro($precoReal*0.25);?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center Verde">
              Liih
              <span class="badge badge-filid badge-pill"><?php echo formatoDinheiro($precoReal*0.15);?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center Verde">
              Empresa
              <span class="badge badge-filid badge-pill"><?php $real=$precoReal-($soma+($precoReal*0.25)+($precoReal*0.15)); echo formatoDinheiro($real);?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center Verde">
              <strong>Aroni Souza</strong>
              <span class="badge badge-filid badge-pill">Valor Empresa</span>
            </li>
          </ul>
        </div>
      </div>

      <div class="row mt-3">
        <div class="col">
          <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center Verde">
              <div class="row">
                <div class="col-auto">
                  Lucros Real por Receita: <strong><?php echo formatoDinheiro(($precoReal*$rendimento)-($custoArrayS+$custoArrayE));?></strong>
                </div>
                <div class="col-auto">
                  Lucros Real por Unidade: <strong><?php echo formatoDinheiro((($precoReal*$rendimento)-($custoArrayS+$custoArrayE))/$rendimento);?></strong>
                </div>
              </div>
            </li>
          <ul>
        </div> 
      </div>

      <div class="alert alert-secondary mt-3" role="alert">
        <strong>Tabela de Sabanete</strong>
      </div>
      <table class="table table-sm table-bordered table-hover">
        <thead class="thead-dark">
          <tr>
            <th scope="col">Materia</th>
            <th scope="col">Volume</th>
            <th scope="col">Preço Pago</th>
            <th scope="col">Preço (Kg, Lt)</th>
            <th scope="col">Frete</th>
            <th scope="col">Valor C/ Frete</th>
            <th scope="col">Qnt Utilizado</th>
            <th scope="col">Custo</th>
          </tr>
        </thead>
        <tbody>
          <?php echo $tabelaSabonete;?>
        </tbody>
      </table>

      <div class="alert alert-secondary mt-3" role="alert">
        <strong>Tabela de Embalagem</strong>
      </div>
      <table class="table table-sm table-bordered table-hover">
        <thead class="thead-dark">
          <tr>
            <th scope="col">Materia</th>
            <th scope="col">Volume</th>
            <th scope="col">Preço Pago</th>
            <th scope="col">Preço (Kg, Lt)</th>
            <th scope="col">Frete</th>
            <th scope="col">Valor C/ Frete</th>
            <th scope="col">Qnt Utilizado</th>
            <th scope="col">Custo</th>
          </tr>
        </thead>
        <tbody>
          <?php echo $tabelaEmbalagem;?>
        </tbody>
      </table>

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
