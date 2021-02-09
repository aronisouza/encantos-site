<?php

/**
 * Login.class
 * Responável por autenticar, validar, e checar usuário do sistema de login!
 */
class Login extends Conn {

    private $Level;
    private $Email;
    private $Senha;
    private $Error;
    private $Result;

    private $Data;
    private $Format;
    
    /**
     * Verifica E-mail: Executa validação de formato de e-mail. Se for um email válido retorna true, ou retorna false.
     * @param STRING $Email = Uma conta de e-mail
     * @return BOOL = True para um email válido, ou false
     */
    public function Email($Email) {
        $this->Data = (string) $Email;
        $this->Format = '/[a-z0-9_\.\-]+@[a-z0-9_\.\-]*[a-z0-9_\.\-]+\.[a-z]{2,4}$/';

        if (preg_match($this->Format, $this->Data)):
            return true;
        else:
            return false;
        endif;
    }

    /**
     * Informar Level: Informe o nível de acesso mínimo para a área a ser protegida.
     * @param INT $Level = Nível mínimo para acesso
     */
    function __construct($Level) {
        $this->Level = (int) $Level;
    }

    /**
     * Efetuar Login: Envelope um array atribuitivo com índices STRING user [email], STRING pass.
     * Ao passar este array na ExeLogin() os dados são verificados e o login é feito!
     * @param ARRAY $UserData = user [email], pass
     */
    public function ExeLogin(array $UserData) {
        $this->Email = (string) strip_tags(trim($UserData['user']));
        $this->Senha = (string) strip_tags(trim($UserData['pass']));
        $this->setLogin();
    }

    /**
     * Verificar Login: Executando um getResult é possível verificar se foi ou não efetuado
     * o acesso com os dados.
     * @return BOOL $Var = true para login e false para erro
     */
    public function getResult() {
        return $this->Result;
    }

    /**
     * Obter Erro: Retorna um array associativo com uma mensagem e um tipo de erro.
     * @return ARRAY $Error = Array associatico com o erro
     */
    public function getError() {
        return $this->Error;
    }

    /**
     * Checar Login: Execute esse método para verificar a sessão USERLOGIN e revalidar o acesso
     * para proteger telas restritas.
     * @return BOLEAM $login = Retorna true ou mata a sessão e retorna false!
     */
    public function CheckLogin() {
        if (empty($_SESSION['userlogin']) || $_SESSION['userlogin']['user_level'] < $this->Level):
            unset($_SESSION['userlogin']);
            return false;
        else:
            return true;
        endif;
    }

    /**
     * ***************************************
     * **********  PRIVATE METHODS  **********
     * ***************************************
     * Valida os dados e armazena os erros caso existam. Executa o login!
	 */
    private function setLogin() {
        $this->Senha = md5($this->Senha);
        if (!$this->Email || !$this->Senha):
            $this->Error = ['Informe seu E-mail e senha para efetuar o login!', AD_ERROR];
            $this->Result = false;
        elseif (!$this->getUser()):
            $this->Error = ['Os dados informados não são compatíveis!', AD_ALERT];
            $this->Result = false;
        elseif ($this->Result['user_level'] < $this->Level):
            $this->Error = ["Desculpe {$this->Result['user_name']}, você não tem permissão para acessar esta área!", AD_ERROR];
            $this->Result = false;
        else:
            $this->Execute();
        endif;
    }

    /**
	* Vetifica usuário e senha no banco de dados!
	*/
    private function getUser() {
        $read = new Read;
        $read->ExeRead("adm_log", "WHERE user_email = :e AND user_password = :p", "e={$this->Email}&p={$this->Senha}");

        if ($read->getResult()):
            $this->Result = $read->getResult()[0];
            return true;
        else:
            return false;
        endif;
    }

    /**
	* Executa o login armazenando a sessão!
	*/
    private function Execute() {
        if (!session_id()):
            session_start();
        endif;

        $_SESSION['userlogin'] = $this->Result;

        $this->Error = ["Olá {$this->Result['user_name']}, seja bem vindo(a). Aguarde redirecionamento!", AD_SUSSESS];
        $this->Result = true;
    }

}
