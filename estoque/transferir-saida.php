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
  




  
  
  $corJanelaMensagem='bg-branco';
  $textoJanela='Selecione um produto no campo<br /> --- Escolha um Produto ---';
  $jnSVG='exclamation-circle';
  $txCor='tx-preto';
  $select=null;
  $selectre=null;

  function carregaRevendedor() {
    $selectRev2 ='';
    $revendedor = new Read;
    $revendedor->ExeRead('revendedor');
    for ($i=0; $i < $revendedor->getRowCount(); $i++):
      $selectRev2 = $selectRev2."<option value=\"{$revendedor->getResult()[$i]['id']}\">{$revendedor->getResult()[$i]['apelido']}</option>";
    endfor;
  return $selectRev2;
  }


  $preecherForm = [
    'produto_id'      => '',
    'produto_nome'    => '',
    'data_producao'   => '',
    'data_validade'   => '',
    'alteracao'       => '',
    'volume_novo'     => '',
    'quantidade'      => '',
    'producao_id'     => '',
    'data_saida'      => '',
    'destino_id'      => '',
    'destino_apelido' => ''
  ];

  if(isset($_GET['id'])):
    $gg = new Read;
    $gg->ExeRead('producao', 'WHERE id=:id', "id={$_GET['id']}");
    if($gg->getRowCount()):
      $preecherForm['produto_id'] = $gg->getResult()[0]['produto_id'];
      $preecherForm['produto_nome'] = $gg->getResult()[0]['produto_nome'];
      $preecherForm['data_producao'] = $gg->getResult()[0]['data_producao'];
      $preecherForm['data_validade'] = $gg->getResult()[0]['data_validade'];
      $preecherForm['alteracao'] = $gg->getResult()[0]['alteracao'];
      $preecherForm['volume_novo'] = $gg->getResult()[0]['volume_novo'];
      $preecherForm['quantidade'] = $gg->getResult()[0]['quantidade'];
      $preecherForm['producao_id'] = $gg->getResult()[0]['id'];
      
      $corJanelaMensagem='bg-verde';
      $textoJanela='Produto selecionado!<br />
      As informações base do produto já foram carregadas...<br />
      <p>Preecha os outros campos e clique em Transferir</p>';
      $jnSVG='exclamation-circle';
      $txCor='tx-preto';
    else:
      $corJanelaMensagem='bg-vermelho';
      $textoJanela='Erro: Este produto ainda não foi produzido!';
      $jnSVG='emoji-frown';
      $txCor='tx-preto';
    endif;
    $GerarCombo = new Read;
    $GerarCombo->ExeRead('producao');
    if($GerarCombo->getRowCount()):
      for ($i=0; $i < $GerarCombo->getRowCount(); $i++):
        $st= $GerarCombo->getResult()[$i]['id']==$_GET['id']?'SELECTED':'';
        if($GerarCombo->getResult()[$i]['quantidade']>=1)
          $select = $select."<option {$st} value=\"{$GerarCombo->getResult()[$i]['id']}\">{$GerarCombo->getResult()[$i]['produto_nome']} | Data: {$GerarCombo->getResult()[$i]['data_producao']} | QNT: [{$GerarCombo->getResult()[$i]['quantidade']}]</option>";
      endfor;
      $selectre = carregaRevendedor();
    endif;
  else:
    $GerarCombo = new Read;
    $GerarCombo->ExeRead('producao');
    if($GerarCombo->getRowCount()):
      for ($i=0; $i < $GerarCombo->getRowCount(); $i++):
        if($GerarCombo->getResult()[$i]['quantidade']>=1)
          $select = $select."<option value=\"{$GerarCombo->getResult()[$i]['id']}\">{$GerarCombo->getResult()[$i]['produto_nome']} | Data: {$GerarCombo->getResult()[$i]['data_producao']} | QNT: [{$GerarCombo->getResult()[$i]['quantidade']}]</option>";
      endfor;
    endif;
    $selectre = carregaRevendedor();
  endif;

  if(isset($_GET['retorno']) && $_GET['retorno']=='Ok'):
    $revendedor = new Read;
    $revendedor->ExeRead('revendedor','WHERE id=:id', "id={$_GET['destino']}");


    $corJanelaMensagem='bg-verde';
    $textoJanela="
      Produto transferido com sucesso!!!<br />
      Foi transferido [ {$_GET['qnt']} ] {$_GET['produto']} para <strong>{$_GET['destino']}</strong>
      ";
    $jnSVG='emoji-smile';
    $txCor='tx-preto';
  elseif(isset($_GET['retorno']) && $_GET['retorno']=='Nok'):
    $corJanelaMensagem='bg-vermelho';
    $textoJanela='Não foi possivel realizar o lançamento...';
    $jnSVG='emoji-frown';
    $txCor='tx-preto';
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
        <li class="nav-item"><a class="nav-link active">Transferir para SAIDA</a></li>
        <li class="nav-item"><a class="nav-link" href="informar-venda.php">Informar VENDA</a></li>
        <li class="nav-item"><a class="nav-link" href="fiado.php">Fiado</a></li>
      </ul>
      
    </div>
  </nav>
  <main role="main" class="container">
    
    <div class="alert <?php echo $corJanelaMensagem;?> mt-3 shadow-sm" role="alert">
      <div class="d-flex">
        <div class="p-4 flex-shrink-1">
          <?php echo getSvg($jnSVG, '63', $txCor); ?>
        </div>
        <div class="p-2 w-100">
          <h5 class="alert-heading <?php echo $txCor; ?>">Transferencia para Revenda</h5>
          <hr>
          <p class="<?php echo $txCor; ?>"><?php echo $textoJanela;?></p>
        </div>
      </div>
    </div>

    <div class="my-3 p-3 bg-branco rounded shadow-sm">
      <h6 class="border-bottom border-gray pb-2 mb-0">Estoque</h6>
     
      <form class="mt-4" id="formid" method="post" action="function/create.php" enctype='multipart/form-data'>

        <input type="hidden" id="producao_id" name="producao_id" value="<?php echo $preecherForm['producao_id'];?>">
        <input type="hidden" id="produto_id" name="produto_id" value="<?php echo $preecherForm['produto_id'];?>">
        <input type="hidden" id="alteracao" name="alteracao" value="<?php echo $preecherForm['alteracao'];?>">
        <input type="hidden" id="volume_novo" name="volume_novo" value="<?php echo $preecherForm['volume_novo'];?>">

        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="produto_nome">Selecione o Produto já Produzido</label>
            <select id="produto_nome" name="produto_nome" class="form-control hover" value="<?php echo $preecherForm['produto_nome'];?>">
              <option selected disabled><strong>--- Escolha um Produto ---</strong></option>
              <?php echo $select;?>
            </select>
          </div>
          <div class="form-group col-md-2">
            <label for="data_producao">Data de Produção</label>
            <input type="text" class="form-control" id="data_producao" name="data_producao" value="<?php echo $preecherForm['data_producao'];?>" readonly >
          </div>
          <div class="form-group col-md-2">
            <label for="data_validade">Validade</label>
            <input type="text" class="form-control" id="data_validade" name="data_validade" value="<?php echo $preecherForm['data_validade'];?>" readonly >
          </div>

          <div class="form-group col-md-2">
            <label for="quantidade">QNT Produção</label>
            <input type="number" class="form-control" id="quantidade" name="quantidade" value="<?php echo $preecherForm['quantidade'];?>" readonly >
          </div>
        </div>


        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="destino_id">Para quem será enviado?</label>
            <select id="destino_id" name="destino_id" class="form-control hover" value="<?php echo $preecherForm['produto_nome'];?>">
              <option selected disabled><strong>--- Revendedores ---</strong></option>
              <?php echo $selectre;?>
            </select>
          </div>

    <!-- 'destino_apelido' => '' -->


          <div class="form-group col-md-3">
            <label for="quantidadeTrans">QNT a ser Transferido?</label>
            <input type="number" class="form-control" id="quantidadeTrans" name="quantidadeTrans" min="0" max="<?php echo $preecherForm['quantidade'];?>">
          </div>
          <div class="form-group col-md-3">
            <label for="data_saida">Data de Saida</label>
            <input type="date" class="form-control" id="data_saida" name="data_saida">
          </div>
          <div class="form-group mt-96 ml-2">
            <button class="btn btn-outline-info" type="submit" value="transferir" name="lacarProducao">Transferir</button>
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
<script>
  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  });


$(document).ready(function(){

  var qnti = $('#quantidade').val();

  $("#produto_nome").change(function()
  {
	  $(location).attr('href', 'http://localhost/encantosdoflorescer/estoque/transferir-saida.php?id='+$('#produto_nome').val());
  });

  $('#quantidadeTrans').change(function()
  {
    var qs = $('#quantidadeTrans').val() <=0? 0: $('#quantidadeTrans').val();
    var ss = qnti-qs;
    $('#quantidade').val(ss);
  });

  $('#formid').on('keyup keypress', function(e) {
  var keyCode = e.keyCode || e.which;
    if (keyCode === 13) { 
      e.preventDefault();
      return false;
    }
  });

});

</script>
</body>
</html>