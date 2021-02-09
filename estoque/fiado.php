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
  
  $tabelaSabonete = null;
  $exibir = false;

  function geraTabela () {
    $tabela = null;
    $lerVen = new Read;
    $lerVen->FullRead("SELECT * FROM `vendas` WHERE `forma_pagamento`='Fiado'");
    if ( $lerVen->getRowCount() >0):
      for($i = 0 ; $i < $lerVen->getRowCount(); $i++):

        if(isset($_GET['editar'])):
          $sexo = $_GET['editar'] != $lerVen->getResult()[$i]['id']?"<a class=\"btn btn-outline-success botaoW\" href=\"?editar={$lerVen->getResult()[$i]['id']}\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Atualizar Fiado\"></a>":"";
        else:
          $sexo = "<a class=\"btn btn-outline-success botaoW\" href=\"?editar={$lerVen->getResult()[$i]['id']}\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Atualizar Fiado\"></a>";
        endif;

      $tabela = $tabela ."
        <tr>
          <th>{$lerVen->getResult()[$i]['produto_nome']}</th>
          <td>{$lerVen->getResult()[$i]['data_venda']}</td>
          <td>{$lerVen->getResult()[$i]['quantidade']}</td>
          <td>{$lerVen->getResult()[$i]['valor_total']}</td>
          <td>{$lerVen->getResult()[$i]['origem_apelido']}</td>
          <td>{$lerVen->getResult()[$i]['obs_forma_pagamento']}</td>
          <td>
            <a href=\"../relatorios/index.php?link={$lerVen->getResult()[$i]['producao_id']}\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Abrir informação dessa Produção\">
              {$lerVen->getResult()[$i]['producao_id']}
            </a>
          </td>
          <td>{$sexo}</td>
        </tr>";
      endfor;
    endif;
    return $tabela;
  }

  function Pesquisa ($tabela , $link, $produto) {
    $lerLink = new Read;
    $lerLink->FullRead("SELECT * FROM `{$tabela}` WHERE `{$produto}`='{$link}'");
    return $lerLink->getResult();
  }

  if(isset($_GET['link'])):
    getPre(Pesquisa('producao', $_GET['link'], 'id'));

    getPreA(Pesquisa('saidas', $_GET['link'], 'produto_id'));

    getPreA(Pesquisa('vendas', $_GET['link'], 'produto_id'));

    die;
  endif;


  if(isset($_POST['venderConfirme']) && isset($_GET['editar'])):
    $formEdita = [
      'forma_pagamento'     => filter_input(INPUT_POST, 'forma_pagamento', FILTER_DEFAULT) ,
      'obs_forma_pagamento' => filter_input(INPUT_POST, 'obs_forma_pagamento', FILTER_DEFAULT)
    ];
    $ulp = new Update;
    $ulp->ExeUpdate('vendas', $formEdita, "WHERE id=:id", "id={$_GET['editar']}");
    header("Location: fiado.php");
  elseif(isset($_GET['editar'])):
    $exibir = true;
    $tabelaSabonete = geraTabela();
  else:
    $tabelaSabonete = geraTabela();
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
        <li class="nav-item"><a class="nav-link active">Fiado</a></li>
      </ul>
    </div>
  </nav>

  <main role="main" class="container">
    <div class="my-3 p-3 bg-branco rounded shadow-sm">
      <h6 class="border-bottom border-gray pb-2 mb-0">Vendas que ainda não foram PAGAS</h6>
      <?php
      if($exibir):
      echo "
        <form id=\"formid\" method=\"post\" enctype=\"multipart/form-data\">
        
          <div class=\"form-row mt-3\">
            <div class=\"col-md-3\">
              <label for=\"forma_pagamento\">Forma de Pagamento</label>
              <select id=\"forma_pagamento\" name=\"forma_pagamento\" class=\"form-control hover\">
                <option>Mercado Pago</option>
                <option>PicPay</option>
                <option>Máquina de Cartão</option>
                <option>NuBank</option>
                <option>Itaú</option>
                <option>Caixa</option>
                <option>Dinheiro</option>
                <option>Outros</option>
                <option>Fiado</option>
                <option>Quebras</option>
              </select>
            </div>
            <div class=\"col-md-7\">
              <label for=\"obs_forma_pagamento\">Obs na Forma de Pagamento</label>
              <input type=\"text\" class=\"form-control\" id=\"obs_forma_pagamento\" name=\"obs_forma_pagamento\">
            </div>
            <div class=\"form-group mt-96 ml-2\">
              <button class=\"btn btn-outline-secondary\" type=\"submit\" name=\"venderConfirme\">Confirmar Edição</button>
            </div>
          </div>

        </form>
      ";
      endif;
      ?>

      <table class="table table-sm table-bordered table-hover mt-3">
        <thead class="thead-dark">
          <tr>
            <th scope="col">Produto</th>
            <th scope="col">Data de Venda</th>
            <th scope="col">Quantidade</th>
            <th scope="col">Valor</th>
            <th scope="col">Vendedor</th>
            <th scope="col">Obs</th>
            <th scope="col">Id de Produção</th>
            <th scope="col">Editar</th>
          </tr>
        </thead>
        <tbody>
          <?php echo $tabelaSabonete;?>
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
