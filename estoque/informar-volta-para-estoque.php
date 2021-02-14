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
  $textoJanela='
    Escolha revendedor para listar os produtos que estão com ele<br />
    Depois escolha o produto que voltará para estoque
  ';

  $jnSVG='exclamation-circle';
  $txCor='tx-preto';
  $select=null;
  $selectRev=null;
  $hide = 'hidden';

  function carregaRevendedor() {
    $selectRev2 ='';
    $revendedor = new Read;
    $revendedor->ExeRead('revendedor');
    for ($i=0; $i < $revendedor->getRowCount(); $i++):
      $selectRev2 = $selectRev2."<option value=\"{$revendedor->getResult()[$i]['id']}\">{$revendedor->getResult()[$i]['nome']} | Apelido: {$revendedor->getResult()[$i]['apelido']}</option>";
    endfor;
    return $selectRev2;
  }

  function geraComboProdutos($Cid){
    $select2='';
    $idp = isset($_GET['idProduto'])?$_GET['idProduto']:0;
    $GerarCombo = new Read;
    $GerarCombo->ExeRead('saidas', 'WHERE destino_id=:id', "id={$Cid}");
    if($GerarCombo->getRowCount()):
      for ($i=0; $i < $GerarCombo->getRowCount(); $i++):
        $st= $GerarCombo->getResult()[$i]['id']==$idp ? 'SELECTED': '';
        if($GerarCombo->getResult()[$i]['quantidade']>=1):
          $select2 = $select2."
          <option {$st} value=\"{$GerarCombo->getResult()[$i]['id']}\">
            {$GerarCombo->getResult()[$i]['produto_nome']} | Produção: {$GerarCombo->getResult()[$i]['data_producao']} | QNT: [{$GerarCombo->getResult()[$i]['quantidade']}]
          </option>";
        endif;
      endfor;
    endif;
    return $select2;
  }

  $preecherForm = [
    'id'              => '',
    'produto_id'      => '',
    'produto_nome'    => '',
    'data_producao'   => '',
    'data_validade'   => '',
    'alteracao'       => '',
    'volume_novo'     => '',
    'producao_id'     => '',
    'quantidade'      => '',
    'data_saida'      => '',
    'destino_id'      => '',
    'destino_apelido' => ''
  ];

  $RevenSel = [
    'id'       => '',
    'nome'     => '',
    'apelido'  => '',
    'cpf'      => '',
    'endereco' => '',
    'bairro'   => '',
    'cidade'   => '',
    'comicao'  => '',
    'contato'  => ''

  ];

  // gera select Revendedor quando clica no botão Recarregar Revendedores
  if(isset($_GET['recarregar'])){$selectRev = carregaRevendedor();} 
  
  // gera select Revendedor e combobox com seus produtos
  if(isset($_GET['id'])):
    $revendedor = new Read;
    $revendedor->ExeRead('revendedor', 'WHERE id=:id', "id={$_GET['id']}");
    if($revendedor->getRowCount()):
      $RevenSel['id'] =           $revendedor->getResult()[0]['id'];
      $RevenSel['nome'] =         $revendedor->getResult()[0]['nome'];
      $RevenSel['apelido'] =      $revendedor->getResult()[0]['apelido'];
      $RevenSel['cpf'] =          $revendedor->getResult()[0]['cpf'];
      $RevenSel['endereco'] =     $revendedor->getResult()[0]['endereco'];
      $RevenSel['bairro'] =       $revendedor->getResult()[0]['bairro'];
      $RevenSel['cidade'] =       $revendedor->getResult()[0]['cidade'];
      $RevenSel['comicao'] =      $revendedor->getResult()[0]['comicao'];
      $RevenSel['contato'] =      $revendedor->getResult()[0]['contato'];


      $preecherForm['origem'] = $revendedor->getResult()[0]['id'];
      $preecherForm['origem_nome'] = $revendedor->getResult()[0]['nome'];
    endif;
    // gera select Revendedor
    $selectRev="<option SELECTED value=\"{$RevenSel['id']}\">{$RevenSel['nome']} | Apelido: {$RevenSel['apelido']}</option>";

    // Gera o combobox dos produtos relacionados ao revendedor
    $select = geraComboProdutos($_GET['id']);
    $hide = '';
  else:
    // gera select Revendedor quando entra na página
    if(!isset($_GET['recarregar'])){ $selectRev = carregaRevendedor();}
  endif;

  // carrega combobox selecionado e junto as informações do produto nos inputs
  if(isset($_GET['idProduto'])):
    //gera combobox de produtos
    $select = geraComboProdutos($_GET['id']);
    // pega as informações da tabela SAIDAS
    $pegaProd = new Read;
    $pegaProd->ExeRead('saidas', 'WHERE id=:id', "id={$_GET['idProduto']}");
    $preecherForm['id']               =$pegaProd->getResult()[0]['id']; // 18
    $preecherForm['produto_id']       =$pegaProd->getResult()[0]['produto_id']; // 1
    $preecherForm['produto_nome']     =$pegaProd->getResult()[0]['produto_nome'];
    $preecherForm['data_producao']    =$pegaProd->getResult()[0]['data_producao'];
    $preecherForm['data_validade']    =$pegaProd->getResult()[0]['data_validade'];
    $preecherForm['alteracao']        =$pegaProd->getResult()[0]['alteracao'];
    $preecherForm['volume_novo']      =$pegaProd->getResult()[0]['volume_novo'];
    $preecherForm['producao_id']      =$pegaProd->getResult()[0]['producao_id'];
    $preecherForm['quantidade']       =$pegaProd->getResult()[0]['quantidade'];
    $preecherForm['data_saida']       =$pegaProd->getResult()[0]['data_saida'];
    $preecherForm['destino_id']       =$pegaProd->getResult()[0]['destino_id'];
    $preecherForm['destino_apelido']  =$pegaProd->getResult()[0]['destino_apelido'];
  endif;

  if(isset($_GET['retorno']) && $_GET['retorno']=='Ok'):
    $corJanelaMensagem='bg-verde';
    $dindin = formatoDinheiro($_GET['valort']);
    $textoJanela="
      Venda informada com sucesso!!!<br />
      O Produto: <strong>{$_GET['produto']}</strong> foi vendido por: <strong>{$_GET['vendedor']}</strong><br />
      Quantidade vendida [ <strong>{$_GET['qnt']}</strong> ] valor total da venda <strong>{$dindin}</strong>
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
        <li class="nav-item"><a class="nav-link" href="transferir-saida.php">Transferir para SAIDA</a></li>
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
          <h5 class="alert-heading <?php echo $txCor; ?>">Informar uma volta para estoque</h5>
          <hr>
          <p class="<?php echo $txCor; ?>"><?php echo $textoJanela;?></p>
        </div>
      </div>
    </div>
    
    <div class="my-3 p-3 bg-branco rounded shadow-sm">
      <h6 class="border-bottom border-gray pb-2 mb-0">Selecione um Revendedor</h6>
      
      <div class="form-row mt-4">
        <div class="col-md-6">
          <label for="revendedor">Selecione o Revendedor</label>
          <select id="revendedor" name="revendedor" class="form-control hover">
            <option selected disabled>--- Escolha um Revendedor ---</option>
            <?php echo $selectRev;?>
          </select>
        </div>
        <div class="mt-96 ml-2">
          <a class="btn btn-outline-info" href="?recarregar">Recarregar Revendedores</a>
        </div>
      </div>

      <form class="mt-4" <?php echo $hide;?> id="formid" method="post" action="function/create.php" enctype='multipart/form-data'>
        <h6 class="border-bottom border-gray pb-2 mb-0">Selecione um Produto</h6>
        <input type="hidden" id="idTabReceita"    name="idTabReceita"     value="<?php echo $preecherForm['produto_id'];?>">
        <input type="hidden" id="producao_id"     name="producao_id"      value="<?php echo $preecherForm['producao_id'];?>">
        <input type="hidden" id="produto_nome"    name="produto_nome"     value="<?php echo $preecherForm['produto_nome'];?>">
        <input type="hidden" id="destino_id"      name="destino_id"       value="<?php echo $preecherForm['destino_id'];?>">
        <input type="hidden" id="destino_apelido" name="destino_apelido"  value="<?php echo $preecherForm['destino_apelido'];?>">
        <input type="hidden" id="alteracao"       name="alteracao"        value="<?php echo $preecherForm['alteracao'];?>">
        <input type="hidden" id="volume_novo"     name="volume_novo"      value="<?php echo $preecherForm['volume_novo'];?>">
        <input type="hidden" id="data_saida"      name="data_saida"       value="<?php echo $preecherForm['data_saida'];?>">
        <input type="hidden" id="valor"           name="valor"            value="0,00">

        <div class="form-row mt-4">
          <div class="form-group col-md-6">
            <label for="idTabSaida">Selecione o Produto</label>
            <select id="idTabSaida" name="idTabSaida" class="form-control hover">
              <option selected disabled>--- Escolha um Produto ---</option>
              <?php echo $select;?>
            </select>
          </div>

          <div class="form-group col-md-2">
            <label for="data_producao">Data de Produção</label>
            <input type="text" class="form-control" id="data_producao" name="data_producao" value="<?php echo $preecherForm['data_producao'];?>" readonly >
          </div>

          <div class="form-group col-md-2">
            <label for="data_validade">Data de Validade</label>
            <input type="text" class="form-control" id="data_validade" name="data_validade" value="<?php echo $preecherForm['data_validade'];?>" readonly >
          </div>

          <div class="form-group col-md-2">
            <label for="quantidade">Quantidade</label>
            <input type="text" class="form-control" id="quantidade" name="quantidade" value="<?php echo $preecherForm['quantidade'];?>" readonly >
          </div>
        </div>

        <h6 class="border-bottom border-gray pb-2 mb-3 mt-5">Informações de Volta para estoque</h6>
        
        <div class="form-row">
          <div class="form-group col-md-3">
            <label for="data_venda">Data da volta</label>
            <input type="date" class="form-control" id="data_venda " name="data_venda" >
          </div>

          <div class="form-group col-md-3">
            <label for="quantidadev">Quantidade</label>
            <input type="number" class="form-control" id="quantidadev" name="quantidadev" placeholder="0" min="0" max="<?php echo $preecherForm['quantidade'];?>">
          </div>

          <div class="form-group col-md-3">
            <label for="forma_pagamento">Voltar</label>
            <select id="forma_pagamento" name="forma_pagamento" class="form-control hover">
              <option>Voltou para estoque</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-10">
            <label for="obs_volta">Porque está voltando?</label>
            <input type="text" class="form-control" id="obs_volta" name="obs_volta">
          </div>

          <div class="form-group mt-96 ml-2">
            <button class="btn btn-outline-info" type="submit" value="voltarEstoque" name="lacarProducao">VOLTAR</button>
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

$(document).ready(function(){

  var qnti = $('#quantidade').val();

  $("#valor").maskMoney({ prefix: "R$ ", decimal: ".", thousands: ","});

  $("#revendedor").change(function()
  {
	  $(location).attr('href', 'http://localhost/encantosdoflorescer/estoque/informar-volta-para-estoque.php?id='+$('#revendedor').val());
  });

  $("#idTabSaida").change(function()
  {
	  $(location).attr('href', 'http://localhost/encantosdoflorescer/estoque/informar-volta-para-estoque.php?id='+$('#revendedor').val()+'&idProduto='+$('#idTabSaida').val());
  });

  $('#quantidadev').change(function()
  {
    var qs = $('#quantidadev').val() <=0? 0: $('#quantidadev').val();
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