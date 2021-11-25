<?php

use App\Core\Controller;

Class Clientes extends Controller{

  public function index(){
    $modelCliente = $this->model("Cliente");
    $dados = $modelCliente->listarTodos();
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    return $dados;
  }

  public function find($id){
    $modelCliente = $this->model("Cliente");
    $dado = $modelCliente->buscarPorId($id);
    if($dado){
      echo json_encode($dado, JSON_UNESCAPED_UNICODE);
    }else{
      http_response_code(404);
      $erro = ["Erro" => "Cliente não encontrado"];
      echo json_encode($erro, JSON_UNESCAPED_UNICODE);
    }
    return $dado;
  }

  public function store(){
    $json = file_get_contents("php://input");
    $dadosInsercao = json_decode($json);
    $modelCliente = $this->model('Cliente');
    $modelCliente->idSexo = $dadosInsercao->idSexo;
    $modelCliente->nome = $dadosInsercao->nome;
    $modelCliente->email = $dadosInsercao->email;
    $modelCliente->senha = $dadosInsercao->senha;
    $modelCliente->telefone = $dadosInsercao->telefone;
    $modelCliente->dataNascimento = $dadosInsercao->dataNascimento;
    $modelCliente->foto = $dadosInsercao->foto;
    $modelCliente->inserirCliente();

    $modelEnderecoCLiente = $this->model("EnderecoCliente");
    $modelEnderecoCLiente->idCliente = $modelCliente->idCliente;
    $modelEnderecoCLiente->uf = $dadosInsercao->uf;
    $modelEnderecoCLiente->cidade = $dadosInsercao->cidade;
    $modelEnderecoCLiente->bairro = $dadosInsercao->bairro;
    $modelEnderecoCLiente->rua = $dadosInsercao->rua;
    $modelEnderecoCLiente->numero = $dadosInsercao->numero;
    $modelEnderecoCLiente->complemento = $dadosInsercao->complemento;
    $modelEnderecoCLiente->cep = $dadosInsercao->cep;
    $modelEnderecoCLiente->inserirEnderecoCliente();
    return $modelCliente;
  }

  public function update($id){
    $json = file_get_contents("php://input");
    $modelCliente = $this->model("Cliente");
    $dadosEdicao = json_decode($json);
    $modelCliente = $modelCliente->buscarPorId($id);

    if(!$modelCliente){
      http_response_code(404);
      $erro = ["erro" => "Cliente não encontrado"];
      echo json_encode($erro);
      exit;
    }

    $dadosEdicao = json_decode($json);
    $file_chunks = explode(";base64,", $dadosEdicao->foto);
    $fileType = explode("image/", $file_chunks[0]);
    $image_type = $fileType[1];
    $base64Img = base64_decode($file_chunks[1]);
    $file = uniqid().'.'.$image_type;
    file_put_contents($file, $base64Img);

    $modelCliente->idSexo = $dadosEdicao->idSexo;
    $modelCliente->nome = $dadosEdicao->nome;
    $modelCliente->email = $dadosEdicao->email;
    $modelCliente->senha = $dadosEdicao->senha;
    $modelCliente->descricao = $dadosEdicao->descricao;
    $modelCliente->telefone = $dadosEdicao->telefone;
    $modelCliente->dataNascimento = $dadosEdicao->dataNascimento;
    $modelCliente->foto = $file;

    $modelEnderecoCLiente = $this->model("EnderecoCliente");
    $modelEnderecoCLiente->idCliente = $id;
    $modelEnderecoCLiente->uf = $dadosEdicao->uf;
    $modelEnderecoCLiente->cidade = $dadosEdicao->cidade;
    $modelEnderecoCLiente->bairro = $dadosEdicao->bairro;
    $modelEnderecoCLiente->rua = $dadosEdicao->rua;
    $modelEnderecoCLiente->numero = $dadosEdicao->numero;
    $modelEnderecoCLiente->complemento = $dadosEdicao->complemento;
    $modelEnderecoCLiente->cep = $dadosEdicao->cep;

    if($modelCliente->atualizar() && $modelEnderecoCLiente->updateEnderecoCliente()){
      http_response_code(204);
    }else{
      http_response_code(500);
      $erro = ["erro" => "Problemas ao editar o cliente"];
      echo json_encode($erro, JSON_UNESCAPED_UNICODE);
    }
  }

  public function delete($id){ 
    $modelCliente = $this->model("Cliente");
    $modelCliente->buscarPorId($id);
    if(!$modelCliente){
      http_response_code(404);
      $erro = ["erro" => "Cliente não encontrado"];
      echo json_encode($erro);  
    }
    if($modelCliente->deletar()){
      http_response_code(204);
    }else{
      http_response_code(500);
      $erro = ["erro" => "Problemas ao deletar cliente"];
      echo json_encode($erro);
    }
    $modelCliente = $modelCliente->deletar();
  }
}