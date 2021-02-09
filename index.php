<?php
  require('app/class/Config.inc.php');
  $session = new Session;
  $login = new Login(3);

  if($login->CheckLogin()):
    $logoff = filter_input(INPUT_GET, 'logoff', FILTER_VALIDATE_BOOLEAN);
    
    if (!$login->CheckLogin()):
      unset($_SESSION['userlogin']);
      header('Location: http://$_SERVER[HTTP_HOST]/encantosdoflorescer/index.php');
    else :
      $userlogin = $_SESSION['userlogin'];
    endif;

    if ($logoff):
      unset($_SESSION['userlogin']);
      header('Location: http://$_SERVER[HTTP_HOST]/encantosdoflorescer/index.php');
    endif;
  endif;
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Encantos Finanças</title>
    <!-- Bootstrap core CSS -->
    <link href="app/assets/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="app/css/offcanvas.css" rel="stylesheet">

  </head>

<body class="bg-body">
    
  <nav class="navbar navbar-expand-sm fixed-top navbar-dark bg-dark container-menu tx-branco">
    Encantos do Florescer :: LOGIN ::
  </nav>

  <main role="main" class="container">
  
    

    <div class="my-3 p-3 bg-white rounded shadow-sm">
      <h6 class="border-bottom border-gray pb-2 mb-0">Digite suas informações de Acesso!!!</h6>

        <div class="media pt-3">

          <div class="col-md-auto">
          <?php

            $login = new Login(3);
            if ($login->CheckLogin()):
              header('Location: receitas/');
            endif;
            $dataLogin = filter_input_array(INPUT_POST, FILTER_DEFAULT);
            if (!empty($dataLogin['AdminLogin'])):
              $login->ExeLogin($dataLogin);
              if (!$login->getResult()):
                getMsgAlert('LOGIN::',$login->getError()[0], $login->getError()[1]);
              else:
                getMsgAlert('LOGIN::',$login->getError()[0], $login->getError()[1]);
                header('Location:  receitas/');
              endif;
            endif;
            $get = filter_input(INPUT_GET, 'exe', FILTER_DEFAULT);
            if (!empty($get)):
              switch ($get):
                case 'restrito':
                  getMsgAlert('LOGIN::','<strong>Opss: </strong>Acesso negado! Efetue seu Login.', AD_ERROR);
                  break;
                case 'logoff':
                  getMsgAlert('LOGIN::','<strong>Sucesso : </strong>Você deslogou corretamente! Sua sessão foi finalizada.', AD_SUSSESS);
                  break;
                case 'mdsenha':
                  getMsgAlert('LOGIN::','<strong>Sucesso: </strong>A senha foi alterada. Efetue seu Login novamente!', AD_INFO);
                  break;
                default : getMsgAlert('LOGIN::','<strong>Opss: </strong>Efetue seu Login novamente!', AD_ERROR);
              endswitch;
            endif;


          ?>
          </div>
          <div class="col-md-auto">
          <form class="mt-4" id="formid" method="post" enctype='multipart/form-data'>
            <div class="panel-body">
              <fieldset>
                <div class="form-group">
                <label for="exampleInputEmail1">Email</label>
                  <input class="form-control" placeholder="Usuário" name="user" type="text" autofocus>
                </div>
                <div class="form-group">
                <label for="exampleInputEmail1">Senha</label>
                  <input class="form-control" placeholder="Senha" name="pass" type="password">
                </div>
              </fieldset>
              <hr>
              <button type="submit" class="form-btn" name="AdminLogin" value="Logar"><i class="glyphicon glyphicon-export"> </i> Entrar</button>
            </div>
          </form>
          </div>
        </div>
      </div>

      <small class="d-block text-right mt-3">
        <a href="#">@ Aroni Souza</a>
      </small>
    </div>
  </main>
  <script src="app/java/jquery-3.5.1.slim.min.js"></script>
  <script src="app/assets/dist/js/bootstrap.bundle.min.js"></script>
  <script src="app/java/offcanvas.js"></script>

  <script>
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    });
  </script>
</body>
</html>
