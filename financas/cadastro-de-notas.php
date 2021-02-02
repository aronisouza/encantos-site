<?php
  require('../app/class/Config.inc.php');
  
  if (isset($_POST['salvarNota'])):
    $Form = [
      'loja' => filter_input(INPUT_POST, 'loja', FILTER_DEFAULT),
      'loja_id' => filter_input(INPUT_POST, 'loja_id', FILTER_DEFAULT),
      'desconto' => filter_input(INPUT_POST, 'desconto', FILTER_DEFAULT),
      'valor' => filter_input(INPUT_POST, 'valor', FILTER_DEFAULT),
      'forma_pagamento' => filter_input(INPUT_POST, 'forma_pagamento', FILTER_DEFAULT),
      'parcela_outro' => 'filid',
      'data_compra' => filter_input(INPUT_POST, 'data_compra', FILTER_DEFAULT),
      'imagem' => $_FILES['imagem']['name']
    ];

    $vowels = array(" ", "R$", ",");
    $Form['valor'] = str_replace($vowels, "",  $Form['valor']);
    $Form['desconto'] = str_replace($vowels, "",  $Form['desconto']);

    // getPreA($Form);

    if(filter_input(INPUT_POST, 'forma_pagamento', FILTER_DEFAULT) == 'Credito Parcelado' || filter_input(INPUT_POST, 'forma_pagamento', FILTER_DEFAULT) == 'Outro'):
      $Form['parcela_outro'] = filter_input(INPUT_POST, 'parcela_outro', FILTER_DEFAULT);
    endif;
    
    if($Form['parcela_outro'] != 'filid' && $Form['parcela_outro'] =='' || empty($Form['loja']) || empty($Form['loja_id']) || empty($Form['valor']) || empty($Form['forma_pagamento']) || empty($Form['data_compra']) || empty($Form['imagem'])):
      $corJanelaMensagem = 'bg-amarelo';
      $txCor = 'tx-preto';
      $jnSVG = 'exclamation-circle';
      $textoJanela = '
        Você tem que informar todos os campos!!!
      ';
    else:
      $read = new Read;
      $read->ExeRead('nota_fiscal', 'WHERE loja_id=:loja_id', "loja_id={$Form['loja_id']}");
      if(!$read->getRowCount()):
        $upload = new Upload($_FILES['imagem'], 1000, 800, "notas-img/");
        $Form['imagem'] = $upload->salvar($Form['loja_id'], Date('Ymd'));
        if($Form['parcela_outro'] =='filid') $Form['parcela_outro']='0';
        $cadNota = new Create;
        $cadNota->ExeCreate('nota_fiscal', $Form);
        if($cadNota->getResult()):
           $corJanelaMensagem = 'bg-branco';
           $txCor = 'tx-azul';
           $jnSVG = 'emoji-heart-eyes';
           $textoJanela = "NOTA cadastrada com sucesso!<br>
           Agora você já poderá cadastrar os itens contidos nela.<br />
           Para isto acessar o menu Cadastro de Itens.<br />
           <img class=\"mt-3\" src=\"{$Form['imagem']}\" width=\"200\">";
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
          NOTA com n°: {$Form['loja_id']} já cdastrada no sistema.<br />
          Favor verificar e caso tenha certeza que estou errado, informar para o Aroni Souza
        ";
      endif;
    endif;
  else:
    $corJanelaMensagem = 'bg-branco';
    $txCor = 'tx-azul';
    $jnSVG = 'exclamation-circle';
    $textoJanela = '
      Aqui você irá cadastrar as Notas dos itens e equipamentos que foram adiquiridos.<br />
      Após informar todos os valores clicar em SALVAR
      <p>Apenas campo `<strong>Desconto</strong>` pode ser deixado vazio</p>
    ';
  endif;
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
        <li class="nav-item active"><a class="nav-link">Cadastro de Notas</a></li>
        <li class="nav-item"><a class="nav-link" href="cadastro-de-itens.php">Cadastro de itens</a></li>
        <li class="nav-item"><a class="nav-link" href="relatorios.php">Relatorio</a></li>
        <li class="nav-item"><a class="nav-link" href="notas.php">Notas</a></li>
      </ul>
      <form class="form-inline my-2 my-lg-0" method="get">
        <input class="form-control mr-sm-2" type="text" placeholder="N° NOTA" name="idnota" disabled>
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit" name="pesquisa" value="envia" disabled><?php echo getSvg('search', '20', 'tx-branco');?></button>
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
        <h5 class="alert-heading <?php echo $txCor;?>">Cadastro de Notas</h5>
        <hr>
        <p class="<?php echo $txCor; ?>"><?php echo $textoJanela;?></p>
      </div>
    </div>
  </div>
  <div class="my-3 p-3 bg-white rounded shadow-sm">
    <h6 class="border-bottom border-gray pb-2 mb-0">Informe todos os campos</h6>
    <form class="mt-4" id="formid" method="post" enctype='multipart/form-data'>
      <div class="form-row">
        <div class="form-group col-md-5">
          <label for="loja">Loja</label>
          <input type="text" class="form-control" id="loja" name="loja">
        </div>
        <div class="form-group col-md-3">
          <label for="loja_id">Número da Nota</label>
          <input type="text" data-accept-dot="1" data-accept-comma="1" class="only-number form-control" id="loja_id" name="loja_id">
        </div>
        <div class="form-group col-md-2">
          <label for="desconto"><strong>Desconto</strong></label>
          <input type="text" class="form-control" id="desconto" name="desconto">
        </div>
        <div class="form-group col-md-2">
          <label for="valor">Valor total</label>
          <input type="text" class="form-control" id="valor" name="valor">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group col-md-3">
          <label for="forma_pagamento">Forma de Pagamento</label>
          <select id="forma_pagamento" name="forma_pagamento" class="form-control hover">
            <option>Credito Vista</option>
            <option>Credito Parcelado</option>
            <option>Debito</option>
            <option>Dinheiro</option>
            <option>Outro</option>
          </select>
        </div>
        <div class="form-group col-md-6" id="divoutro">
          <label for="parcela_outro" id="lboutro"></label>
          <input type="text" class="form-control hoverInte" id="parcela_outro" name="parcela_outro">
        </div>
        <div class="form-group col-md-3">
          <label for="data_compra">Data da Compra</label>
          <input type="date" class="form-control hover" id="data_compra" name="data_compra">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group col-md-5">
          <label for="imagem">Informe a imagem da nota</label>
          <input type="file" class="form-control-file hover" id="imagem" name="imagem">
        </div>
        <div class="form-group col-md-6"></div>
        <div class="form-group mt-4">
          <button class="btn btn-outline-info" type="submit" name="salvarNota">Salvar</button>
        </div>
      </div>
    </form>
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
    $("#divoutro").hide();
    $("#desconto").maskMoney({
        prefix: "R$ ",
        decimal: ".",
        thousands: ","
    });
    $("#valor").maskMoney({
        prefix: "R$ ",
        decimal: ".",
        thousands: ","
    });
  });
  
  $("#forma_pagamento").change(function()
  {
    if($("#forma_pagamento").val() == 'Credito Parcelado' || $("#forma_pagamento").val() == 'Outro')
    {
      $("#divoutro").show();
      $("#lboutro").text(($("#forma_pagamento").val() == 'Outro'?'Como foi a forma de Pagamento?': 'Em quantas parcelas?'));
    }
    else
    {
      $("#divoutro").hide();
    }
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
