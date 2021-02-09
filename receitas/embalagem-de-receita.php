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

  $URL_ATUAL= "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  $nomeReceita = isset($_GET['nomereceita']) ? $_GET['nomereceita'] : 0;
  
  $valido = false;
  $exibirTabela = false;
  $pesquisaRetorno = null;
  $formItem =null;

  // Pegar as informacções da receita {INICIO}
  if (isset($_GET['nomereceita'])):
    // SE CLICAR NO BOTÃO DE PESQUISA {INICIO}
    if(isset($_GET['pesquisa'])):
      $pesquisa = new Read;
      $pesquisa->ExeRead('receita', 'WHERE nome=:nomereceita', "nomereceita={$nomeReceita}");
      if($pesquisa->getRowCount()):
        header("Location: embalagem-de-receita.php?idreceita={$pesquisa->getResult()[0]['id']}&nomereceita={$pesquisa->getResult()[0]['nome']}&rendimento={$pesquisa->getResult()[0]['rendimento']}&tipo={$pesquisa->getResult()[0]['tipo']}");
      else:
        // se nota não existe
        $nomeReceita = $nomeReceita==''? '<strong>vazio</strong>': $nomeReceita;
        $corJanelaMensagem = 'bg-branco';
        $txCor = 'tx-vermelho';
        $jnSVG = 'exclamation-circle';
        $textoJanela = "
          Esta Receita não Existe!!!<br />
          Você informou Receita: {$nomeReceita}<br />
          <p>O nome da receita deve ser o mesmo cadastrado.<br />
          Caso não lembre olhar na página ". getSvg('home', '20', 'tx-azul')." no CARD Receitas.</p>
        ";
      endif;
      // SE CLICAR NO BOTÃO DE PESQUISA {FINAL}
    else:
      if(!isset($_POST['adicionar'])):
        $pega = new Read;
        $pega->ExeRead('receitas_embalagem', 'WHERE id_receita=:idreceita ORDER BY id DESC', "idreceita={$_GET['idreceita']}");
        if($pega->getRowCount()>0):
          for($i = 0 ; $i < $pega->getRowCount(); $i++):
            $ft = getPorcerto($pega->getResult()[$i]['frete']);
            $pesquisaRetorno = $pesquisaRetorno ."
              <tr>
                <th scope=\"row\">{$pega->getResult()[$i]['id']}</th>
                <td>{$pega->getResult()[$i]['materia']}</td>
                <td>{$pega->getResult()[$i]['volume']}</td>
                <td>{$pega->getResult()[$i]['utilizado']}</td>
                <td>{$ft}</td>
                <td>
                  <a class=\"btn btn-danger botaoW\" href=\"?apagar={$pega->getResult()[$i]['id']}\"></a>  
                </td>
                <td>
                <a class=\"btn btn-info botaoW\" href=\"?editar={$pega->getResult()[$i]['id']}\"></a>  
              </td>
              </tr>
            ";
          endfor;
          $exibirTabela = true;
        endif;
      endif;
      $corJanelaMensagem = 'bg-verde';
      $txCor = 'tx-preto';
      $jnSVG = 'emoji-heart-eyes';
      $textoJanela = "
        <strong>Receita:</strong> {$_GET['nomereceita']}<br />
        <strong>ID:</strong> {$_GET['idreceita']}<br />
        <strong>Tipo:</strong> {$_GET['tipo']}
      ";
    endif;
  else:
    $corJanelaMensagem = 'bg-azul';
    $txCor = 'tx-preto';
    $jnSVG = 'exclamation-circle';
    $textoJanela = '
      Aqui você irá cadastrar a Embalagem de sua Receita.<br />
      Para isto você utiliza o campo de busca [ Nome da Receita ] ... Lembrando que o nome tem que ser exatamente como cadastrado no livro.<br />
      Após a pesquisa você verá as informações da Receita. Se estiver correto você poderá adicionar os itens no formulário logo a baixo.<br />
      <p>Você só poderá enviar um item por vez.<br />
      Cada item inserido será mostrado na tela.</p>
    ';
  endif; 
  // Pegar as informacções da receita {FINAL}

  // Faz o adição de itens na tabela de itens {INICIO}
  if (isset($_POST['adicionar'])):
    $formItem = [
      'id_receita' => $_GET['idreceita'],
      'materia' => filter_input(INPUT_POST, 'materia', FILTER_DEFAULT),
      'volume' => filter_input(INPUT_POST, 'volume', FILTER_DEFAULT),
      'preco_pago' => filter_input(INPUT_POST, 'preco_pago', FILTER_DEFAULT),
      'utilizado' => filter_input(INPUT_POST, 'utilizado', FILTER_DEFAULT),
      'frete' => filter_input(INPUT_POST, 'frete', FILTER_DEFAULT),
      'tipo_volume' => filter_input(INPUT_POST, 'tipo_volume', FILTER_DEFAULT)
    ];

    $vowels = array(" ", "R$", ",", "۞", "%", "Ω");
    $formItem['preco_pago'] = str_replace($vowels, "",  $formItem['preco_pago']);
    $formItem['volume'] = str_replace($vowels, "",  $formItem['volume']);
    $formItem['utilizado'] = str_replace($vowels, "",  $formItem['utilizado']);
    $formItem['frete'] = str_replace($vowels, "",  $formItem['frete']);

    $cadItemNota = new Create;
    $cadItemNota->ExeCreate('receitas_embalagem', $formItem);
    
    if($cadItemNota->getResult()):
      // fazer logica de mostrar itens salvos
      $pega = new Read;
      $pega->ExeRead('receitas_embalagem', 'WHERE id_receita=:idreceita ORDER BY id DESC', "idreceita={$_GET['idreceita']}");
      if($pega->getRowCount()>0):
        for($i = 0 ; $i < $pega->getRowCount(); $i++):
          $ft = getPorcerto($pega->getResult()[$i]['frete']);
          $pesquisaRetorno = $pesquisaRetorno ."
            <tr>
              <th scope=\"row\">{$pega->getResult()[$i]['id']}</th>
              <td>{$pega->getResult()[$i]['materia']}</td>
              <td>{$pega->getResult()[$i]['volume']}</td>
              <td>{$pega->getResult()[$i]['utilizado']}</td>
              <td>{$ft}</td>
              <td>
                <a class=\"btn btn-danger botaoW\" href=\"?apagar={$pega->getResult()[$i]['id']}\"></a>  
              </td>
              <td>
                <a class=\"btn btn-info botaoW\" href=\"?editar={$pega->getResult()[$i]['id']}\"></a>  
              </td>
            </tr>
          ";
        endfor;
        $exibirTabela = true;
      endif;
    else:
      $corJanelaMensagem = 'bg-vermelho';
      $txCor = 'tx-preto';
      $jnSVG = 'emoji-frown';
      $textoJanela = '
        Ops!!!<br />
        Algo de errado não deu muito certo...
      ';
    endif;
  endif;
  // Faz o adição de itens na tabela de itens {FINAL}

  // APAGAR UM ITEM DA RECEITA {INICIO}
  if (isset($_GET['apagar'])):
    $ap_id = $_GET['apagar'];
    $del = new Delete;
    $del->ExeDelete('receitas_embalagem',"WHERE id=:id", "id={$ap_id}");
    if($del->getRowCount()>0):
      $pega = new Read;
      $pega->ExeRead('receitas_embalagem', 'WHERE id_receita=:idreceita ORDER BY id DESC', "idreceita={$_GET['idreceita']}");
      if($pega->getRowCount()>0):
        for($i = 0 ; $i < $pega->getRowCount(); $i++):
          $ft = getPorcerto($pega->getResult()[$i]['frete']);
          $pesquisaRetorno = $pesquisaRetorno ."
            <tr>
              <th scope=\"row\">{$pega->getResult()[$i]['id']}</th>
              <td>{$pega->getResult()[$i]['materia']}</td>
              <td>{$pega->getResult()[$i]['volume']}</td>
              <td>{$pega->getResult()[$i]['utilizado']}</td>
              <td>{$ft}</td>
              <td>
                <a class=\"btn btn-danger botaoW\" href=\"?apagar={$pega->getResult()[$i]['id']}\"></a>  
              </td>
              <td>
                <a class=\"btn btn-info botaoW\" href=\"?editar={$pega->getResult()[$i]['id']}\"></a>  
              </td>
            </tr>
          ";
        endfor;
        $exibirTabela = true;
      endif;
    endif;
  endif;
  // APAGAR UM ITEM DA RECEITA {FINAL}

  // EDITA UM ITEM DA RECEITA {INICIO}
  if (isset($_GET['editar'])):
    $ap_id = $_GET['editar'];

    $formItem = [
      'id_receita' => filter_input(INPUT_POST, 'id_receita', FILTER_DEFAULT),
      'materia' => filter_input(INPUT_POST, 'materia', FILTER_DEFAULT),
      'volume' => filter_input(INPUT_POST, 'volume', FILTER_DEFAULT),
      'preco_pago' => filter_input(INPUT_POST, 'preco_pago', FILTER_DEFAULT),
      'utilizado' => filter_input(INPUT_POST, 'utilizado', FILTER_DEFAULT),
      'frete' => filter_input(INPUT_POST, 'frete', FILTER_DEFAULT),
      'tipo_volume' => filter_input(INPUT_POST, 'tipo_volume', FILTER_DEFAULT)
    ];

    $vowels = array(" ", "R$", ",", "۞", "%", "Ω");
    $formItem['preco_pago'] = str_replace($vowels, "",  $formItem['preco_pago']);
    $formItem['volume'] = str_replace($vowels, "",  $formItem['volume']);
    $formItem['utilizado'] = str_replace($vowels, "",  $formItem['utilizado']);
    $formItem['frete'] = str_replace($vowels, "",  $formItem['frete']);

    $upl = new Update;
    $upl->ExeUpdate('receitas_embalagem',$formItem,"WHERE id=:id", "id={$ap_id}");
    if($upl->getRowCount()>0):
      $pega = new Read;
      $pega->ExeRead('receitas_embalagem', 'WHERE id_receita=:idreceita ORDER BY id DESC', "idreceita={$_GET['idreceita']}");
      if($pega->getRowCount()>0):
        for($i = 0 ; $i < $pega->getRowCount(); $i++):
          $ft = getPorcerto($pega->getResult()[$i]['frete']);
          $pesquisaRetorno = $pesquisaRetorno ."
            <tr>
              <th scope=\"row\">{$pega->getResult()[$i]['id']}</th>
              <td>{$pega->getResult()[$i]['materia']}</td>
              <td>{$pega->getResult()[$i]['volume']}</td>
              <td>{$pega->getResult()[$i]['utilizado']}</td>
              <td>{$ft}</td>
              <td>
                <a class=\"btn btn-danger botaoW\" href=\"?apagar={$pega->getResult()[$i]['id']}\"></a>  
              </td>
              <td>
                <a class=\"btn btn-info botaoW\" href=\"?editar={$pega->getResult()[$i]['id']}\"></a>  
              </td>
            </tr>
          ";
        endfor;
        $exibirTabela = true;
      endif;
    endif;
  endif;
  // EDITA UM ITEM DA RECEITA {FINAL}

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
      <li class="nav-item"><a class="nav-link" href="adicionar-receita.php">Adicionar Receita</a></li>
      <li class="nav-item"><a class="nav-link" href="itens-de-receita.php">Itens de uma Receita</a></li>
      <li class="nav-item active"><a class="nav-link">Embalagem de uma Receita</a></li>
    </ul>
    <form class="form-inline my-2 my-lg-0" method="get">
      <input class="form-control mr-sm-2" type="text" placeholder="Nome Receita" name="nomereceita">
      <button class="btn btn-outline-success my-2 my-sm-0" type="submit" name="pesquisa" value="envia"><?php echo getSvg('search', '20', 'tx-branco');?></button>
    </form>
  </div>

</nav>

<main role="main" class="container">
  
  <div class="alert <?php echo $corJanelaMensagem;?> mt-3 shadow-sm" role="alert">
    <div class="d-flex">
      <div class="p-4 flex-shrink-1">
        <?php echo getSvg($jnSVG, '63', $txCor);?>
      </div>
      <div class="p-2 w-100">
        <h5 class="alert-heading <?php echo $txCor;?>">Cadastro de Itens de uma Nota</h5>
        <hr>
        <p class="<?php echo $txCor; ?>"><?php echo $textoJanela;?></p>
      </div>
    </div>
  </div>

  <div class="my-3 p-3 bg-white rounded shadow-sm">
    
    <h6 class="border-bottom border-gray pb-2 mb-0">Adicionar Embalagem a uma Receita</h6>
    
    <form class="mt-4" id="formid" method="post" enctype='multipart/form-data'>

    <input type="hidden" id="id_receita" name="id_receita" value="<?php echo $formItem['id_receita'];?>">

      <div class="form-row">
        
        <div class="form-group col-md-6">
          <label for="materia">Material</label>
          <input type="text" class="form-control" id="materia" name="materia">
        </div>
        <div class="form-group col-md-3">
          <label for="volume">Volume ( gr, ml, cm )</label>
          <input type="text" class="form-control" id="volume" name="volume">
        </div>
        <div class="form-group col-md-3">
          <label for="preco_pago">Preço Pago Nota</label>
          <input type="text" class="form-control" id="preco_pago" name="preco_pago">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group col-md-3">
          <label for="frete">Frete ( 10% padrão )</label>
          <input type="text" class="form-control" id="frete" name="frete">
        </div>
        <div class="form-group col-md-3">
          <label for="utilizado">Quantidade Utilizada</label>
          <input type="text" class="form-control" id="utilizado" name="utilizado">
        </div>
        <div class="form-group col-md-4">
          <label for="tipo_volume">Tipo de Volume</label>
          <select id="tipo_volume" name="tipo_volume" class="form-control hover">
            <option>Gramas</option>
            <option>Mililitros</option>
            <option>Centimetros</option>
            <option>Unidade</option>
          </select>
        </div>
        <div class="form-group mt-96 ml-2">
          <button class="btn btn-outline-info" type="submit" name="adicionar">Adicionar a Receita</button>
        </div>
      </div>

    </form>
    <!-- mostrar os itens aqui -->
    <?php 
      if($exibirTabela):
        echo "<div class=\"mt-4\"></div>";
        echo "
        <table class=\"table table-sm table-bordered\">
          <thead>
            <tr>
              <th scope=\"col\">#</th>
              <th scope=\"col\">Materia</th>
              <th scope=\"col\">Volume</th>
              <th scope=\"col\">Utilizado</th>
              <th scope=\"col\">Frete</th>
              <th class=\"th\" scope=\"col\">Deletar</th>
              <th class=\"th\" scope=\"col\">Editar</th>
            </tr>
          </thead>
          <tbody>
            {$pesquisaRetorno}
          </tbody>
        </table>
        ";
      endif;
    ?>
    <!-- mostrar os itens aqui -->
    <?php
      if($nomeReceita!=0 && $valido):
        echo "
        <h6 class=\"border-bottom border-gray pb-2 mb-0\">
        <div class=\"d-flex align-items-center\">
          <strong>Esperando por uma Nota válida...</strong>
          <div class=\"spinner-border ml-auto\" style=\"width: 2rem; height: 2rem;\" role=\"status\" aria-hidden=\"true\"></div>
        </div>
        </h6>
        ";
      endif;
    ?>

    <small class="d-block text-right mt-3">
      <a href="#">@ Aroni Souza</a>
    </small>
  </div>

</main>

<script src="../app/java/jquery-3.5.1.slim.min.js"></script>
<script src="../app/assets/dist/js/bootstrap.bundle.min.js"></script>
<script src="../app/java/offcanvas.js"></script>
<script src="../app/java/jquery-maskmoney.js"></script>

</body>
<script>
  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  });


  $(document).ready(function()
  {
    $("#preco_pago").maskMoney({ prefix: "R$ ", decimal: ".", thousands: ","});
    $("#utilizado").maskMoney({ decimal: ".", thousands: ".", prefix: "Ω ", precision: 3});
    $("#volume").maskMoney({ decimal: ".", thousands: ".", prefix: "۞ ", precision: 3});
    $("#frete").maskMoney({ decimal: ".", thousands: ".", prefix: "% ", precision: 2});
  });
  
  $('#formid').on('keyup keypress', function(e) {
  var keyCode = e.keyCode || e.which;
    if (keyCode === 13) { 
      e.preventDefault();
      return false;
    }
  });

  jQuery(function($) {
    $(document).on('keypress', 'input.only-number', function(e) {
      var $this = $(this);
      var key = (window.event)?event.keyCode:e.which;
      var dataAcceptDot = $this.data('accept-dot');
      var dataAcceptComma = $this.data('accept-comma');
      var acceptDot = (typeof dataAcceptDot !== 'undefined' && (dataAcceptDot == true || dataAcceptDot == 1)?true:false);
      var acceptComma = (typeof dataAcceptComma !== 'undefined' && (dataAcceptComma == true || dataAcceptComma == 1)?true:false);

      if((key > 47 && key < 58)
        || (key == 46 && acceptDot)
        || (key == 44 && acceptComma)) {
        return true;
      } else {
        return (key == 8 || key == 0)?true:false;
      }
    });
  });

</script>
</html>