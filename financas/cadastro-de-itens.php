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

  $idNota = isset($_GET['idnota']) ? $_GET['idnota'] : 0;
  $valido = false;
  $pesquisaRetorno = null;
  $exibirTabela = false;
  $URL_ATUAL= "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  
  $formEdita = [
    'produto'        => '',
    'valor'          => '',
    'valor_produto'  => '',
    'peso'           => '',
    'quantidade'     => '',
    'unidade_medida' => '',
    'categoria'      => '',
    'cor'            => '',
    'nota_id'        => '',
    'data_compra'    => ''
  ];

  function getTh ($id, $produto, $categoria, $quantidade, $valor, $URLATUAL){
    $Retorno ="
      <th scope=\"row\">{$id}</th>
      <td>{$produto}</td>
      <td>{$categoria}</td>
      <td>{$quantidade}</td>
      <td>{$valor}</td>
      <td>
        <a class=\"btn btn-danger botaoW\" href=\"{$URLATUAL}&apagar={$id}\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"DELETAR => Ao clicar ele será deletado automaticamente... Perdeu!!!\"></a>  
      </td>
      <td>
        <a class=\"btn btn-info botaoW\" href=\"{$URLATUAL}&editar={$id}\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"EDITAR este item\"></a>
      </td>
    </tr>";
    return $Retorno;
  }
  
  // Pegar as informacções da receita {INICIO}
  if (isset($_GET['idnota'])):
    // SE CLICAR NO BOTÃO DE PESQUISA {INICIO}
    if(isset($_GET['pesquisa'])):
      $pesquisa = new Read;
      $pesquisa->ExeRead('nota_fiscal', 'WHERE loja_id=:loja_id', "loja_id={$idNota}");
      if($pesquisa->getRowCount()):
        header("Location: cadastro-de-itens.php?idnota={$idNota}&datanota={$pesquisa->getResult()[0]['data_compra']}&loja={$pesquisa->getResult()[0]['loja']}");
      else:
        // se nota não existe
        $idNota = $idNota==''? '<strong>vazio</strong>': $idNota;
        $corJanelaMensagem = 'bg-branco';
        $txCor = 'tx-vermelho';
        $jnSVG = 'exclamation-circle';
        $textoJanela = "
          Número de Nota invalido!!!<br />
          Você informou N° Nota: {$idNota}<br />
          <p>Você só poderá enviar um item por vez.<br />
          Cada item inserido será mostrado na tela.</p>
        ";
      endif;
      // SE CLICAR NO BOTÃO DE PESQUISA {FINAL}
    else:
      if(!isset($_POST['adicionar'])):
        $valido = true;
        $pega = new Read;
        $pega->ExeRead('notas_itens', 'WHERE nota_id=:nota ORDER BY id DESC', "nota={$idNota}");
        if($pega->getRowCount()>0):
          for($i = 0 ; $i < $pega->getRowCount(); $i++):
            $pesquisaRetorno = $pesquisaRetorno.getTh($pega->getResult()[$i]['id'],$pega->getResult()[$i]['produto'],$pega->getResult()[$i]['categoria'],$pega->getResult()[$i]['quantidade'],$pega->getResult()[$i]['valor'],$URL_ATUAL);
          endfor;
          $exibirTabela = true;
        endif;
      endif;
      $corJanelaMensagem = 'bg-branco';
      $txCor = 'tx-preto';
      $jnSVG = 'emoji-heart-eyes';
      $textoJanela = "
        Estabelecimento: <strong> {$_GET['loja']} </strong><br />
        N° Nota: <strong> {$_GET['idnota']} </strong><br />
        Data da Compra: <strong> {$_GET['datanota']} </strong>
      ";
    endif;
  else:
    $corJanelaMensagem = 'bg-branco';
    $txCor = 'tx-azul';
    $jnSVG = 'exclamation-circle';
    $textoJanela = '
      Aqui você irá cadastrar os Itens de uma Nota.<br />
      Para isto você utiliza o campo de busca [ N° NOTA ]<br />
      Após a pesquisa você verá as informações da Nota. Se estiver correto você poderá adicionar os itens no formulário logo a baixo.<br />
      <p>Você só poderá enviar um item por vez.<br />
      Cada item inserido será mostrado na tela.</p>
    ';
  endif;
  // Pegar as informacções da receita {FINAL}

  // Faz o adição de itens na tabela de itens {INICIO}
  if (isset($_POST['adicionar']) && !isset($_GET['editar'])):
    $formItem = [
      'produto' => filter_input(INPUT_POST, 'produto', FILTER_DEFAULT),
      'valor' => filter_input(INPUT_POST, 'valor', FILTER_DEFAULT),
      'valor_produto' => filter_input(INPUT_POST, 'valor_produto', FILTER_DEFAULT),
      'peso' => filter_input(INPUT_POST, 'peso', FILTER_DEFAULT),
      'quantidade' => filter_input(INPUT_POST, 'quantidade', FILTER_DEFAULT),
      'unidade_medida' => filter_input(INPUT_POST, 'unidade_medida', FILTER_DEFAULT),
      'categoria' => filter_input(INPUT_POST, 'categoria', FILTER_DEFAULT),
      'cor' => filter_input(INPUT_POST, 'cor', FILTER_DEFAULT),
      'nota_id' => $_GET['idnota'],
      'data_compra' => $_GET['datanota']
    ];

    $vowels = array(" ", "R$", ",", "֍");
    $formItem['valor'] = str_replace($vowels, "",  $formItem['valor']);
    $formItem['peso'] = str_replace($vowels, "",  $formItem['peso']);

    $cadItemNota = new Create;
    $cadItemNota->ExeCreate('notas_itens', $formItem);
    
    if($cadItemNota->getResult()):
      // fazer logica de mostrar itens salvos
      $pega = new Read;
      //$pega->FullRead("SELECT * FROM `notas_itens` WHERE ``=");
      $pega->ExeRead('notas_itens', 'WHERE nota_id=:nota ORDER BY id DESC', "nota={$idNota}");
      if($pega->getRowCount()>0):
        for($i = 0 ; $i < $pega->getRowCount(); $i++):
          $pesquisaRetorno = $pesquisaRetorno.getTh($pega->getResult()[$i]['id'],$pega->getResult()[$i]['produto'],$pega->getResult()[$i]['categoria'],$pega->getResult()[$i]['quantidade'],$pega->getResult()[$i]['valor'],$URL_ATUAL);
        endfor;
        $valido = true;
        $exibirTabela=true;
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
    $del->ExeDelete('notas_itens',"WHERE id=:id", "id={$ap_id}");
    if($del->getRowCount()>0):
      $pega = new Read;
      $pega->ExeRead('notas_itens', 'WHERE nota_id=:nota ORDER BY id DESC', "nota={$idNota}");
      if($pega->getRowCount()>0):
        for($i = 0 ; $i < $pega->getRowCount(); $i++):
          $pesquisaRetorno = $pesquisaRetorno.getTh($pega->getResult()[$i]['id'],$pega->getResult()[$i]['produto'],$pega->getResult()[$i]['categoria'],$pega->getResult()[$i]['quantidade'],$pega->getResult()[$i]['valor'],$URL_ATUAL);
        endfor;
        $exibirTabela = true;
      endif;
    endif;
  endif;
  // APAGAR UM ITEM DA RECEITA {FINAL}

  // CARREGA OS ITENS NOS CAMPOS PARA EDITAR {INICIO}
  if (isset($_GET['editar']) && !isset($_POST['adicionar'])):
    $ed_id = $_GET['editar'];
    $pega = new Read;
    $pega->ExeRead('notas_itens', 'WHERE id=:idr', "idr={$ed_id}");
    $formEdita = [
      'produto'        => $pega->getResult()[0]['produto'],
      'valor'          => $pega->getResult()[0]['valor'],
      'valor_produto'  => $pega->getResult()[0]['valor_produto'],
      'peso'           => $pega->getResult()[0]['peso'],
      'quantidade'     => $pega->getResult()[0]['quantidade'],
      'unidade_medida' => $pega->getResult()[0]['unidade_medida'],
      'categoria'      => $pega->getResult()[0]['categoria'],
      'cor'            => $pega->getResult()[0]['cor'],
      'nota_id'        => $pega->getResult()[0]['nota_id'],
      'data_compra'    => $pega->getResult()[0]['data_compra']
    ];
    $corJanelaMensagem = 'bg-laranja';
    $txCor = 'tx-preto';
    $jnSVG = 'chat-dots';
    $textoJanela = "
      Você esta em uma página de Edição<br />
      Certifique-se que todos os campos estão com valores corretos.
      <p>Você esta Editando: <strong>{$formEdita['produto']}</strong></p>
    ";
  endif;
  // CARREGA OS ITENS NOS CAMPOS PARA EDITAR {FINAL}

  // EDITAR UM ITEM DA RECEITA {INICIO}
  if (isset($_GET['editar']) && isset($_POST['adicionar'])):
    $ed_id = $_GET['editar'];
    $pega = new Read;
    $pega->ExeRead('notas_itens', 'WHERE id=:idr', "idr={$ed_id}");
    
    
    $formEdita = [
      'produto' => filter_input(INPUT_POST, 'produto', FILTER_DEFAULT),
      'valor' => filter_input(INPUT_POST, 'valor', FILTER_DEFAULT),
      'valor_produto' => filter_input(INPUT_POST, 'valor_produto', FILTER_DEFAULT),
      'peso' => filter_input(INPUT_POST, 'peso', FILTER_DEFAULT),
      'quantidade' => filter_input(INPUT_POST, 'quantidade', FILTER_DEFAULT),
      'unidade_medida' => filter_input(INPUT_POST, 'unidade_medida', FILTER_DEFAULT),
      'categoria' => filter_input(INPUT_POST, 'categoria', FILTER_DEFAULT),
      'cor' => filter_input(INPUT_POST, 'cor', FILTER_DEFAULT),
      'nota_id' => $_GET['idnota'],
      'data_compra' => $_GET['datanota']
    ];
     
    //mudar isso aqui
    $vowels = array(" ", "R$", ",", "۞", "%", "Ω");
    $formEdita['valor'] = str_replace($vowels, "",  $formEdita['valor']);
    $formEdita['valor_produto'] = str_replace($vowels, "",  $formEdita['valor_produto']);
    $formEdita['peso'] = str_replace($vowels, "",  $formEdita['peso']);
    
    $ulp = new Update;
    $ulp->ExeUpdate('notas_itens', $formEdita, "WHERE id=:id", "id={$ed_id}");
    
    if($ulp->getRowCount()>0):
      for($i = 0 ; $i < $ulp->getRowCount(); $i++):
        $pesquisaRetorno = $pesquisaRetorno.getTh($pega->getResult()[$i]['id'],$pega->getResult()[$i]['produto'],$pega->getResult()[$i]['categoria'],$pega->getResult()[$i]['quantidade'],$pega->getResult()[$i]['valor'],$URL_ATUAL);
      endfor;
      $corJanelaMensagem = 'bg-verde';
      $txCor = 'tx-preto';
      $jnSVG = 'shift';
      $textoJanela = '
        Ops!!!<br />
      ';
    else:
      // header("Location: index.php");
      $corJanelaMensagem = 'bg-vermelho';
      $txCor = 'tx-preto';
      $jnSVG = 'emoji-frown';
      $textoJanela = '
        Ops!!!<br />
        Algo de errado não deu muito certo...
      ';
    endif;
    
  endif;
  // EDITAR UM ITEM DA RECEITA {FINAL}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Encantos Finanças</title>
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
        <li class="nav-item active"><a class="nav-link">Cadastro de itens</a></li>
        <li class="nav-item"><a class="nav-link" href="relatorios.php">Relatorio</a></li>
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

    <?php
      if($idNota!=0 && $valido): 
        echo "<div class=\"mt-4\"></div>";
      ?>
      <h6 class="border-bottom border-gray pb-2 mb-0">Um item por vez!</h6>
      <form class="mt-4" id="formid" method="post" enctype='multipart/form-data'>
        <div class="form-row">

          <div class="form-group col-md-6">
            <label for="produto">Produto</label>
            <input type="text" class="form-control" id="produto" name="produto" value="<?php echo $formEdita['produto'];?>">
          </div>

          <div class="form-group col-md-2">
            <label for="peso">Peso</label>
            <input type="text" class="form-control" id="peso" name="peso" placeholder="֍ 0.000" value="<?php echo $formEdita['peso'];?>">
          </div>

          <div class="form-group col-md-2">
            <label for="unidade_medida">Tipo Medida</label>
            <select id="unidade_medida" name="unidade_medida" class="form-control hover">
              <option>Gramas</option>
              <option>Mililitros</option>
              <option>Centimetros</option>
              <option>Unidade</option>
            </select>
          </div>

          <div class="form-group col-md-2">
            <label for="quantidade">Quantidade</label>
            <input type="text" class="form-control" id="quantidade" name="quantidade" value="<?php echo $formEdita['quantidade'];?>">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-3">
            <label for="categoria">Categoria</label>
            <select id="categoria" name="categoria" class="form-control hover">
              <option>Embalagem</option>
              <option>Equipamento</option>
              <option>Matéria Prima</option>
            </select>
          </div>

          <div class="form-group col-md-2">
            <label for="valor">Valor Pago</label>
            <input type="text" class="form-control" id="valor" name="valor" placeholder="R$ 0.00" value="<?php echo $formEdita['valor'];?>">
          </div>
          
          <div class="form-group col-md-2">
            <label for="valor_produto">Valor Unitário</label>
            <input type="text" class="form-control" id="valor_produto" name="valor_produto" placeholder="R$ 0.00" value="<?php echo $formEdita['valor_produto'];?>">
          </div>

          <div class="form-group col-md-3">
            <label for="cor">Cor</label>
            <input class="form-control" type="text" id= "cor" name="cor" value="<?php echo $formEdita['cor'];?>">
          </div>

          <div class="form-group mt-96 ml-2">
            <button class="btn btn-outline-info" type="submit" name="adicionar">Adicionar</button>
          </div>
        </div>

      </form>
      <?php
      else:
        echo "
        <h6 class=\"border-bottom border-gray pb-2 mb-0\">
        <div class=\"d-flex align-items-center\">
          <strong>Esperando por uma Nota válida... </strong>
          <div class=\"spinner-border ml-5\" style=\"width: 2rem; height: 2rem;\" role=\"status\" aria-hidden=\"true\"></div>
        </div>
        </h6>
        ";
      endif; ?>


      <!-- mostrar os itens aqui -->
        <?php 
          if($exibirTabela):
            echo "
            <table class=\"table table-sm table-bordered\">
              <thead>
                <tr>
                  <th scope=\"col\">#</th>
                  <th scope=\"col\">Produto</th>
                  <th scope=\"col\">Categoria</th>
                  <th scope=\"col\">Quantidade</th>
                  <th scope=\"col\">Valor</th>
                  <th class=\"th\" scope=\"col\">Remove</th>
                  <th class=\"th\" scope=\"col\">Edita</th>
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
      

      <small class="d-block text-right mt-3">
        <a href="">@ Aroni Souza</a>
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
    
    $("#valor").maskMoney({
        prefix: "R$ ",
        decimal: ".",
        precision: 2,
        thousands: ","
    });
    $("#valor_produto").maskMoney({
        prefix: "R$ ",
        decimal: ".",
        precision: 2,
        thousands: ","
    });
    $("#peso").maskMoney({
        decimal: ".",
        thousands: ".",
        precision: 3,
        prefix: "֍ "
    });


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
