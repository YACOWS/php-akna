<?php

/**
 * Created by PhpStorm.
 * User: eduardo
 * Date: 29/05/16
 * Time: 00:07
 * 
 * @category Akna
 * @package  Akna_EmailMarketing
 * @author   Daniel Antunes <daniel.antunes.rocha@gmail.com>
 * @license  BSD <http://www.opensource.org/licenses/bsd-license.php>
 * @link     http://github.com/w3p/php-akna
 * @version  0.2
 */
class Akna_EmailMarketing_Action extends Akna_Client
{
    /**
     * @var string Akna inserted message name
     */
    protected $mensagem;

    /**
     * @var string akna action name. We have to generate it to monitor later on
     */
    protected $nome;

    /**
     * @var DateTime Action end date on isoformat %Y-%m-%d
     */
    protected $data_encerramento;

    /**
     * @var string Sender name
     */
    protected $nome_remetente;

    /**
     * @var string sender email
     */
    protected $email_remetente;

    /**
     * @var string Sender return email
     */
    protected $email_retorno;

    /**
     * @var string Message subject
     */
    protected $assunto;

    /**
     * @var string Akna contact list name. It has to be registered already
     */
    protected $lista;

    /**
     * @var DateTime Scheduled sending time. It should be %Y-%m-%d
     */
    protected $datahora;

    public function __construct(
        $username,
        $password,
        $mensagem,
        $nome_remetente,
        $email_remetente,
        $email_retorno,
        $assunto,
        $lista,
        \DateTime $data_encerramento = null,
        \DateTime $datahora = null,
        $company = null
    ) {
        parent::__construct($username, $password, $company = null);

        $this->username = $username;
        $this->password = $password;
        $this->mensagem = $mensagem;
        $this->nome_remetente = $nome_remetente;
        $this->email_remetente = $email_remetente;
        $this->email_retorno = $email_retorno;
        $this->assunto = $assunto;
        $this->lista = $lista;

        if (empty($data_encerramento)) {
            // O padrão é seis meses na frente
            $this->data_encerramento = new \DateTime();
            $this->data_encerramento->add(new \DateInterval('P5M'));
        } else {
            $this->data_encerramento = $data_encerramento;
        }
        $this->datahora = $datahora;
    }

    /**
     * @param $data_encerramento string Data de encerramento no formato string
     */
    public function setDataEncerramento($data_encerramento = null) {
        if (!empty($data_encerramento)) {
            $this->data_encerramento = new \DateTime($data_encerramento);
        } else {
            // Ajusta data de encerramento para tempo máximo de seis meses
            $this->data_encerramento = new \DateTime();
            $this->data_encerramento->add(new \DateInterval('P6M'));
        }
    }

    /**
     * @param $datahora string Data de início da campanha
     */
    public function setDataHora($datahora) {
        $this->datahora = new \DateTime($datahora);
    }
    
    /**
     * @return string Closing date as string
     */
    public function getDataEncerramentoStr() {
        if (empty($this->data_encerramento)) {
            return null;
        }
        return $this->data_encerramento->format('Y-m-d');
    }

    /**
     * @return string Campaign start date as string
     */
    public function getDataHoraStr() {
        return $this->datahora->format('Y-m-d H:i:s');
    }

    /**
     * @return string Action generated name
     */
    public function getNome() {
        return $this->mensagem . "-action";
    }

    /**
     * Create and send message to Akna 
     * 
     * @param bool $response Whether we should return response or not
     * @return bool Success flag
     * @throws Akna_Exception Raises exception on error
     */
    public function create($response = false) {
        $fields = array(
            'nome' => $this->getNome(),
            'mensagem' => $this->mensagem,
            'data_encerramento' => $this->getDataEncerramentoStr(),
            'nome_remetente' => $this->nome_remetente,
            'email_remetente' => $this->email_remetente,
            'email_retorno' => $this->email_retorno,
            'assunto' => $this->assunto,
            'lista' => $this->lista,
            'agendar' => array(
                'datahora' => $this->getDataHoraStr()
            )
        );

        if ($response === false) {
            // Wait for exception on error
            $this->getHttpClient()->send('19.05', 'emkt', $fields);
        } else {
            // Wait for exception on error
            return $this->getHttpClient()->send('19.05', 'emkt', $fields);
        }
        
        return true;
    }
}