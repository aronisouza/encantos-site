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
  




  

  $retornoNota = false;
  $imagemNota = '';
  $pesquisaRetorno = null;
  $notaInfo = [
    'Loja'=>'',
    'valor'=>'',
    'data_compra'=>'',
    'forma_pagamento'=>'',
    'parcela_outro'=>'',
  ];
  if (isset($_GET['idnota'])):
    $idNota = $_GET['idnota'];
    $nota = new Read;
    $nota->ExeRead('nota_fiscal', 'WHERE loja_id=:nota', "nota={$idNota}");
    $pesquisa = new Read;
    $pesquisa->ExeRead('notas_itens', 'WHERE nota_id=:nota', "nota={$idNota}");
    if($pesquisa->getRowCount()):
      $notaInfo = [
        'loja_id'=>$nota->getResult()[0]['loja_id'],
        'loja'=> $nota->getResult()[0]['loja'],
        'valor'=> $nota->getResult()[0]['valor'],
        'data_compra'=> $nota->getResult()[0]['data_compra'],
        'forma_pagamento'=> $nota->getResult()[0]['forma_pagamento'],
        'parcela_outro'=> $nota->getResult()[0]['parcela_outro'],
        'imagem'=> $nota->getResult()[0]['imagem'],
      ];
      for($i = 0 ; $i < $pesquisa->getRowCount(); $i++):
        $pesquisaRetorno = $pesquisaRetorno ."
        <div class=\"d-flex bd-highlight\">
          <div class=\"p-2 flex-fill bd-highlight\">{$pesquisa->getResult()[$i]['produto']}</div>
          <div class=\"p-2 flex-fill bd-highlight text-right\">R$ {$pesquisa->getResult()[$i]['valor']}</div>
        </div>";
      endfor;
      $corJanelaMensagem = 'bg-verde';
      $txCor = 'tx-preto';
      $jnSVG = 'emoji-heart-eyes';
      $textoJanela = "
        Esta é sua N° Nota: {$_GET['idnota']}<br />
        <p>Utilize a busca para ver outra N° Nota</p>
      ";
      $retornoNota = true;
    else:
      $corJanelaMensagem = 'bg-azul';
      $txCor = 'tx-amarelo';
      $jnSVG = 'x-circle';
      $textoJanela = '
        Este N° Nota ainda não tem nada cadastrado.<br />
        <p>Vá até menu Cadastro de itens e cadastre os itens para que possam ser visualizados aqui.</p>
      ';
    endif;
  else:
    $corJanelaMensagem = 'bg-amarelo';
    $txCor = 'tx-azul';
    $jnSVG = 'exclamation-circle';
    $textoJanela = '
      Utilize o campo de busca para vilualizar uma determinada nota.<br />
      Caso a N° Nota informada não exista nada acontece... Apenas será retornada uma mensagem avisando.
      <p>Assim que clicar na lupa sua nota será carregada.</p>
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
        <li class="nav-item"><a class="nav-link" href="cadastro-de-notas.php">Cadastro de Notas</a></li>
        <li class="nav-item"><a class="nav-link" href="cadastro-de-itens.php">Cadastro de itens</a></li>
        <li class="nav-item"><a class="nav-link" href="relatorios.php">Relatorio</a></li>
        <li class="nav-item active"><a class="nav-link">Notas</a></li>
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
        <h5 class="alert-heading <?php echo $txCor;?>">Visualizando uma Nota</h5>
        <hr>
        <p class="<?php echo $txCor;?>"><?php echo $textoJanela;?></p>
      </div>
    </div>
  </div>

  <?php if($retornoNota):?>
  <div class="my-3 p-3 bg-white rounded shadow-sm">
    <h6 class="border-bottom border-gray pb-2 mb-0">Aqui está sua Nota</h6>
      
      <div class="row mt-3">
        
        <div class="col-sm-5">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Itens contido na Nota</h5>
              <?php echo $pesquisaRetorno;?>
            </div>
          </div>
        </div>

        <div class="col-sm-5">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Informação da Nota</h5>
              
              <div class="d-flex bd-highlight">
                <div class="p-2 flex-fill bd-highlight">Loja</div>
                <div class="p-2 flex-fill bd-highlight text-right"><?php echo $notaInfo['loja'];?></div>
              </div>

              <div class="d-flex bd-highlight">
                <div class="p-2 flex-fill bd-highlight">Valor Total</div>
                <div class="p-2 flex-fill bd-highlight text-right">R$ <?php echo $notaInfo['valor'];?></div>
              </div>

              <div class="d-flex bd-highlight">
                <div class="p-2 flex-fill bd-highlight">Data Da Compra</div>
                <div class="p-2 flex-fill bd-highlight text-right"><?php echo $notaInfo['data_compra'];?></div>
              </div>

              <div class="d-flex bd-highlight">
                <div class="p-2 flex-fill bd-highlight">Forma De Pagamento</div>
                <div class="p-2 flex-fill bd-highlight text-right"><?php echo $notaInfo['forma_pagamento'];?></div>
              </div>

              <div class="d-flex bd-highlight">
                <div class="p-2 flex-fill bd-highlight">Parcelas ?</div>
                <div class="p-2 flex-fill bd-highlight text-right"><?php echo $notaInfo['parcela_outro'];?></div>
              </div>

              <?php
              if($notaInfo['parcela_outro']>1):
                $par = number_format(($notaInfo['valor']/$notaInfo['parcela_outro']), 2, '.', '');   ;
                echo "<div class=\"d-flex bd-highlight\">
                <div class=\"p-2 flex-fill bd-highlight\">Valor por Parcela</div>
                <div class=\"p-2 flex-fill bd-highlight text-right\">R$ {$par}</div>
                </div>";
              endif;
              ?>

            </div>
          </div>
        </div>

        <div class="col-sm-2">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Nota</h5>
              <button type="button" class="btn btn-link" data-toggle="modal" data-target="#exampleModal">
                <figure>
                  <img src="<?php echo $notaInfo['imagem'];?>" width="100%" class="mt-3">
                </figure>
              </button>
            </div>
          </div>
        </div>

      </div>

    <small class="d-block text-right mt-3">
      <a href="">@ Aroni Souza</a>
    </small>
  </div>
  <?php endif;?>
</main>


<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Nota Fiscal</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <img src="<?php echo $notaInfo['imagem'];?>" width="100%" class="mt-3">
      </div>
    </div>
  </div>
</div>

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
