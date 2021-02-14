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
  $txCor = 'tx-azul';
  $textoJanela = 'sem';
  $formEdita =null;

  if (!isset($_GET['idReceita']) && isset($_POST['criarReceita'])):
    $receita = [
      'nome'        => filter_input(INPUT_POST, 'nome', FILTER_DEFAULT),
      'medida'      => filter_input(INPUT_POST, 'medida', FILTER_DEFAULT),
      'medida_unidade'      => filter_input(INPUT_POST, 'medida_unidade', FILTER_DEFAULT),
      'tipo'        => filter_input(INPUT_POST, 'tipo', FILTER_DEFAULT),
      'rendimento'  => filter_input(INPUT_POST, 'rendimento', FILTER_DEFAULT),
      'preco_real'  => filter_input(INPUT_POST, 'preco_real', FILTER_DEFAULT),
      'imagem'      => filter_input(INPUT_POST, 'imagem', FILTER_DEFAULT)
    ];
    $vowels = array(" ", "R$", ",", "۞", "%", "Ω");
    $receita['medida'] = str_replace($vowels, "",  $receita['medida']);
    $receita['medida_unidade'] = str_replace($vowels, "",  $receita['medida_unidade']);
    $receita['preco_real'] = str_replace($vowels, "",  $receita['preco_real']);
    if(empty($receita['nome']) || empty($receita['medida']) || empty($receita['tipo']) || empty($receita['rendimento'])):
      $corJanelaMensagem = 'bg-amarelo';
      $txCor = 'tx-preto';
      $jnSVG = 'exclamation-circle';
      $textoJanela = '
        Você tem que informar todos os campos!!!
      ';
    else:
      $upload = new Upload($_FILES['imagem'], 1000, 800, "img-sabonetes/");
      $nomeImagem = tirarAcentos($receita['nome']);
      $receita['imagem'] = $upload->salvar($nomeImagem);
      $read = new Read;
      $read->ExeRead('receita', 'WHERE nome=:nomer', "nomer={$receita['nome']}");
      if(!$read->getRowCount()):
        $cadNota = new Create;
        $cadNota->ExeCreate('receita', $receita);
        if($cadNota->getResult()):
           $corJanelaMensagem = 'bg-branco';
           $txCor = 'tx-azul';
           $jnSVG = 'emoji-heart-eyes';
           $textoJanela = "Receita adicionada com sucesso!<br>
           Agora você já poderá cadastrar os itens contidos nela.<br />
           Para isto acessar o menu Itens de uma Receita.<br />";
        else:
          $corJanelaMensagem = 'bg-vermelho';
          $txCor = 'tx-branco';
          $jnSVG = 'emoji-frown';
          $textoJanela = "
            Não foi possivel realizar o cadastro erro interno...
          ";
        endif;
      else:
        $corJanelaMensagem = 'bg-laranja';
        $txCor = 'tx-azul';
        $jnSVG = 'exclamation-circle';
        $textoJanela = "
          Receita com nome: {$receita['nome']} já cadastrada no sistema.<br />
          Favor verificar e caso tenha certeza que estou errado, informar para o Aroni Souza
        ";
      endif;
    endif;
  else:
    $corJanelaMensagem = 'bg-branco';
    $txCor = 'tx-azul';
    $jnSVG = 'exclamation-circle';
    $textoJanela = '
      Aqui você irá cadastrar as suas Receitas.<br />
      Após informar todos os valores clicar em Criar Receita
      <p>Todos os campos são obrigatórios</p>
    ';
  endif;
  
  // EDITAR CARREGA RECEITA {INICIO}
  if (isset($_GET['idReceita']) && !isset($_POST['criarReceita'])):
    $ed_id = $_GET['idReceita'];
    $pega = new Read;
    $pega->ExeRead('receita', 'WHERE id=:idr', "idr={$ed_id}");
    $formEdita = [
      'id'          => $pega->getResult()[0]['id'],
      'nome'        => $pega->getResult()[0]['nome'],
      'medida'      => $pega->getResult()[0]['medida'],
      'medida_unidade'      => $pega->getResult()[0]['medida_unidade'],
      'tipo'        => $pega->getResult()[0]['tipo'],
      'rendimento'  => $pega->getResult()[0]['rendimento'],
      'preco_real'  => $pega->getResult()[0]['preco_real'],
      'imagem'      => $pega->getResult()[0]['imagem']
    ];
    $corJanelaMensagem = 'bg-laranja';
    $txCor = 'tx-preto';
    $jnSVG = 'chat-dots';
    $textoJanela = "
      Você esta em uma página de Edição<br />
      Certifique-se que todos os campos estão com valores corretos.
      <p>Você esta Editando a Receita: <strong>{$formEdita['nome']}</strong></p>
    ";
  endif;

 // EDITAR UM ITEM DA RECEITA {INICIO}
  if (isset($_GET['idReceita']) && isset($_POST['criarReceita'])):
    $ed_id = $_GET['idReceita'];
    $formItem = [
      'id'          => $ed_id,
      'nome'        => filter_input(INPUT_POST, 'nome', FILTER_DEFAULT),
      'medida'      => filter_input(INPUT_POST, 'medida', FILTER_DEFAULT),
      'medida_unidade'      => filter_input(INPUT_POST, 'medida_unidade', FILTER_DEFAULT),
      'tipo'        => filter_input(INPUT_POST, 'tipo', FILTER_DEFAULT),
      'rendimento'  => filter_input(INPUT_POST, 'rendimento', FILTER_DEFAULT),
      'preco_real'  => filter_input(INPUT_POST, 'preco_real', FILTER_DEFAULT),
      'imagem'      => $_GET['imagem']
    ];
    // se estiver pedindo pra forçar torca de immage
    if(filter_input(INPUT_POST, 'forceimagem',FILTER_DEFAULT) == 'on'):
      if (file_exists($formItem['imagem'])):
        //php excluir diretorio
        unlink($formItem['imagem']);
      endif;
      // faz upload da nova imagem
      $upload = new Upload($_FILES['imagem'], 1000, 800, "img-sabonetes/");
      $nomeImagem = tirarAcentos($formItem['nome']);
      $formItem['imagem'] = $upload->salvar($nomeImagem);
    endif;
    $vowels = array(" ", "R$", ",", "۞", "%", "Ω");
    $formItem['medida'] = str_replace($vowels, "",  $formItem['medida']);
    $formItem['medida_unidade'] = str_replace($vowels, "",  $formItem['medida_unidade']);
    $formItem['preco_real'] = str_replace($vowels, "",  $formItem['preco_real']);
    if($formItem['nome']!=''):
      $ulp = new Update;
      if(filter_input(INPUT_POST, 'forceimagem',FILTER_DEFAULT) == 'on'):
        $troca=['imagem'=>''];
        $ulp->ExeUpdate('receita', $troca, "WHERE id=:id", "id={$ed_id}");
      endif;
      $ulp->ExeUpdate('receita', $formItem, "WHERE id=:id", "id={$ed_id}");
      if($ulp->getRowCount()>0):
        header("Location: index.php");
      else:
        header("Location: #");
      endif;
    else:
      $corJanelaMensagem = 'bg-vermelho';
      $txCor = 'tx-preto';
      $jnSVG = 'emoji-frown';
      $textoJanela = "
        Erro ao Editar!<br />
        Certifique-se que todos os campos estão com valores corretos.<br />
        Caso nada tenha alterado não será possivel editar.<br />
        <strong><i>E você sempre verá esta mensangem!!!</i></strong>
        <p>O nome da Receita é obrigatório</p>
        <p>Você esta Editando a Receita: <strong>{$formEdita['nome']}</strong></p>
      ";
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
      <li class="nav-item active"><a class="nav-link">Adicionar Receita</a></li>
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
    <div class="d-flex">
      <div class="p-4 flex-shrink-1">
        <?php echo getSvg($jnSVG, '63', $txCor); ?>
      </div>
      <div class="p-2 w-100">
        <h5 class="alert-heading <?php echo $txCor; ?>">Adicionar uma Receita</h5>
        <hr>
        <p class="<?php echo $txCor; ?>"><?php echo $textoJanela;?></p>
      </div>
    </div>
  </div>

  <div class="my-3 p-3 bg-white rounded shadow-sm">
    <h6 class="border-bottom border-gray pb-2 mb-0">Adicionando uma Receita ao Livro</h6>
    
    <form class="mt-4" id="formid" method="post" enctype='multipart/form-data'>

      <div class="form-row">
        <div class="form-group col-md-7">
          <label for="nome">Nome da Receita</label>
          <input type="text" class="form-control" id="nome" name="nome" value="<?php echo $formEdita['nome'];?>">
        </div>
        <div class="form-group col-md-2">
          <label for="tipo">Tipo de Produto</label>
          <select id="tipo" name="tipo" class="form-control hover">
            <option>Sabonete</option>
            <option>Shampoo</option>
            <option>Difusor Aromatico</option>
            <option>Vela Aromatico</option>
            <option>Hidratante Corporal</option>
            <option>Óleo Corporal</option>
            <option>Sais de Banho</option>
			<option>Embalagem</option>
          </select>
        </div>
        <div class="form-group col-md-3">
          <label for="preco_real">Preco Real de Venda</label>
          <input type="text" class="form-control" id="preco_real" name="preco_real" value="<?php echo $formEdita['preco_real'];?>">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group col-md-3">
          <label for="medida">Rendimento Volume ( gr, ml )</label>
          <input type="text" class="form-control" id="medida" name="medida" value="<?php echo $formEdita['medida'];?>">
        </div>
        <div class="form-group col-md-3">
          <label for="medida_unidade">Volume Unidade ( gr, ml )</label>
          <input type="text" class="form-control" id="medida_unidade" name="medida_unidade" value="<?php echo $formEdita['medida_unidade'];?>">
        </div>
        <div class="form-group col-md-3">
          <label for="rendimento">Rendimento Unidade</label>
          <input type="number" class="form-control" id="rendimento" name="rendimento" min="0" max="100"value="<?php echo $formEdita['rendimento'];?>">
        </div>
        <div class="form-group col-md-4">
          <label for="imagem">Informe a imagem da nota</label>
          <input type="file" class="form-control-file hover" id="imagem" name="imagem">
        </div>
      </div>

      <div class="form-row">

        <div class="form-group col-md-10 ml-4">
          <input type="checkbox" class="form-check-input" id="forceimagem" name="forceimagem">
          <label class="form-check-label" for="forceimagem">Forçar troca de Imagem (Marque aqui para trocar a imagem)</label>
        </div>
        
        <div class="form-group">
          <button class="btn btn-outline-info" type="submit" name="criarReceita">Criar Receita</button>
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
    $("#medida").maskMoney({
        decimal: ".",
        thousands: ".",
        precision: 3,
        prefix: "۞ "
    });
    $("#medida_unidade").maskMoney({
        decimal: ".",
        thousands: ".",
        precision: 3,
        prefix: "۞ "
    });
    $("#preco_real").maskMoney({
        decimal: ".",
        thousands: ".",
        precision: 2,
        prefix: "R$ "
    });
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
