<?php
date_default_timezone_set("America/Sao_Paulo");

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Models\Entity\Usuario;
use App\Models\Entity\Perfil;
use App\Models\Entity\Profissional;
use App\Models\Entity\AtividadeProfissional;
use App\Models\Entity\Bairro;
use App\Models\Entity\HorarioServico;
use App\Models\Entity\Orcamento;
use App\Models\Entity\SolicitacaoOrcamento;
use App\Models\Entity\StatusOrcamento;
use App\Models\Entity\LocalAtendimento;
use App\Models\Entity\UrgenciaServico;
use Slim\App;
use Slim\Container;
use Doctrine\ORM\EntityManager;
use Slim\Middleware\TokenAuthentication;
use App\Auth\Auth;

/** @var Container $cnt */
$cnt = require_once __DIR__ . '/bootstrap.php';
$entityManager = $cnt[EntityManager::class];
/** @var App $app */
$app = $cnt[App::class];

$authenticator = function($request, TokenAuthentication $tokenAuth){
    /**
     * Try find authorization token via header, parameters, cookie or attribute
     * If token not found, return response with status 401 (unauthorized)
     */
    $token = $tokenAuth->findToken($request);
    /**
     * Call authentication logic class
     */
    $auth = new Auth();
    /**
     * Verify if token is valid on database
     * If token isn't valid, must throw an UnauthorizedExceptionInterface
     */
    $auth->validateToken($token);
};

/**
 * Add token authentication middleware
 */
$app->add(new TokenAuthentication([
    'path' =>   '/api',
    'passthrough' => ['/api/login','/api/cadastro','/api/consulta','/api/validaSemSenha', '/api/alterarSenha/', "/api/imagens/", '/api/usuario/','/api/profissional/' ],
    'authenticator' => $authenticator,
    'secure' => false
]));

$app->options('/{routes}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

// Catch-all route to serve a 404 Not Found page if none of the routes match
// NOTE: make sure this route is defined last
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});

/**
 * Retornando mais informações do usuario informado pelo id
 * @request curl -X GET http://localhost:8000/user/1
 */
$app->get('/api/usuario/{id}', function (Request $request, Response $response) use ($app,$entityManager) {
    $route = $request->getAttribute('route');
    $id = $route->getArgument('id');
    $usersRepository = $entityManager->getRepository('App\Models\Entity\Usuario');
    $user = $usersRepository->find($id);        

    $return = $response->withJson($user, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

$app->post('/api/imagens/{id}', function (Request $request, Response $response) use ($app,$entityManager) {
    $route = $request->getAttribute('route');
    $id = $route->getArgument('id');
    $usersRepository = null;
    $retorno = null;
    if($request->getParam('perfil') == "cliente"){
        $usersRepository = $entityManager->getRepository('App\Models\Entity\Usuario');
        $user = $usersRepository->find($id);
        $retorno = [
            'foto' => $user->getImagem()
        ];
    }elseif($request->getParam('perfil') == "profissional"){
        $usersRepository = $entityManager->getRepository('App\Models\Entity\Profissional');
        $user = $usersRepository->find($id);
        $retorno = [
            'foto' => $user->getImagem(),
            'frenterg' => $user->getFrenterg(),
            'versorg' => $user->getVersorg(),
            'comprovante' => $user->getComprovante()
        ];
    }
   
    $return = $response->withJson($retorno, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

$app->get('/api/profissional/{id}', function (Request $request, Response $response) use ($app,$entityManager) {
    $route = $request->getAttribute('route');
    $id = $route->getArgument('id');
    $usersRepository = $entityManager->getRepository('App\Models\Entity\Profissional');
    $user = $usersRepository->find($id);        

    $return = $response->withJson($user, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});


/**
 * Lista de todos os usuarios
 * @request curl -X GET http://localhost:8000/user
 */
$app->get('/api/usuario', function (Request $request, Response $response) use ($app,$entityManager) {

    $usersRepository = $entityManager->getRepository('App\Models\Entity\Usuario');
    $users = $usersRepository->findAll();

    $return = $response->withJson($users, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

$app->get('/api/profissional', function (Request $request, Response $response) use ($app,$entityManager) {

    $usersRepository = $entityManager->getRepository('App\Models\Entity\Profissional');
    $users = $usersRepository->findAll();

    $return = $response->withJson($users, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

/**
 * Cadastra um novo <Usuario></Usuario>
 * @request curl -X POST http://localhost:8000/user -H "Content-type: application/json" -d '{"name":"O Oceano no Fim do Caminho", "author":"Neil Gaiman"}'
 */
$app->post('/api/cadastro/usuario', function (Request $request, Response $response) use ($app,$entityManager) {

    $params = (object) $request->getParams();
    
    $id = $params->perfil;
    $perfilRepository = $entityManager->getRepository('App\Models\Entity\Perfil');
    $perfil = $perfilRepository->find($id);        

    $id = $params->bairro;
    $bairroRepository = $entityManager->getRepository('App\Models\Entity\Bairro');
    $bairro = $bairroRepository->find($id);        

    $user = new Usuario($params->nome,$params->password,$params->cep,$params->endereco,$params->complemento,$bairro,$params->email,$params->telefone1,$params->telefone2,$params->cpf,$params->imagem, $perfil);
    
    /**
     * Persiste a entidade no banco de dados
     */
    $entityManager->persist($user);
    $entityManager->flush();


    $return = $response->withJson($user, 201)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

$app->post('/api/cadastro/profissional', function (Request $request, Response $response) use ($app,$entityManager) {

    $params = (object) $request->getParams();
    
    $id = $params->perfil;
    $perfilRepository = $entityManager->getRepository('App\Models\Entity\Perfil');
    $perfil = $perfilRepository->find($id);          

    $id = $params->atividade_principal;
    $atividadeRepository = $entityManager->getRepository('App\Models\Entity\AtividadeProfissional');
    $atividade_principal = $atividadeRepository->find($id);
    $id = $params->extra;
    $extra = null;
    if($id != ''){
        $extra = $atividadeRepository->find($id);
    }
    $id = $params->bairro;
    $bairroRepository = $entityManager->getRepository('App\Models\Entity\Bairro');
    $bairro = $bairroRepository->find($id); 
    $profissional = new Profissional($params->nome,$params->fantasia,$params->password,$params->cep,$params->endereco,$params->complemento,$bairro,$params->email,$params->telefone1,$params->telefone2,$params->telefone3,$params->telefone4,$params->cpf,$params->cnpj,$params->frenterg,$params->versorg,$params->comprovante,$params->foto,$atividade_principal,$extra,$params->situacao_cadastral, $perfil,'');
    
    /**
     * Persiste a entidade no banco de dados
     */
    $entityManager->persist($profissional);
    $entityManager->flush();
    

    $return = $response->withJson($profissional, 201)
        ->withHeader('Content-type', 'application/json');
    return $return;
});
/**
 * Atualiza os dados de um Usuario
 * @request curl -X PUT http://localhost:8000/user/14 -H "Content-type: application/json" -d '{"name":"Deuses Americanos", "author":"Neil Gaiman"}'
 */
$app->put('/api/usuario/{id}', function (Request $request, Response $response) use ($app,$entityManager) {

    /**
     * Pega o ID do Usuario informado na URL
     */
    $route = $request->getAttribute('route');
    $id = $route->getArgument('id');

    /**
     * Encontra o Usuario no Banco
     */ 
    $usersRepository = $entityManager->getRepository('App\Models\Entity\Usuario');
    $user = $usersRepository->find($id);   

    $bairroRepository = $entityManager->getRepository('App\Models\Entity\Bairro');
    $bairro = $bairroRepository->find($request->getParam('bairro')); 

    $perfilRepository = $entityManager->getRepository('App\Models\Entity\Perfil');
    $perfil = $perfilRepository->find($request->getParam('perfil'));
    /**
     * Atualiza e Persiste o Usuario com os parâmetros recebidos no request
     */
    $user->setNome($request->getParam('nome'))
        ->setPassword($request->getParam('password'))
        ->setCep($request->getParam('cep'))
        ->setEndereco($request->getParam('endereco'))
        ->setComplemento($request->getParam('complemento'))
        ->setBairro($bairro)
        ->setEmail($request->getParam('email'))
        ->setTelefone1($request->getParam('telefone1'))
        ->setTelefone2($request->getParam('telefone2'))
        ->setCpf($request->getParam('cpf'))
        ->setImagem($request->getParam('imagem'))
        ->setPerfil($perfil);

    /**
     * Persiste a entidade no banco de dados
     */
    $entityManager->persist($user);
    $entityManager->flush();        

    
    $return = $response->withJson($user, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

$app->put('/api/profissional/{id}', function (Request $request, Response $response) use ($app,$entityManager) {

    /**
     * Pega o ID do Usuario informado na URL
     */
    $route = $request->getAttribute('route');
    $id = $route->getArgument('id');

    /**
     * Encontra o Usuario no Banco
     */ 
    $usersRepository = $entityManager->getRepository('App\Models\Entity\Profissional');
    $user = $usersRepository->find($id);   

    $bairroRepository = $entityManager->getRepository('App\Models\Entity\Bairro');
    $bairro = $bairroRepository->find($request->getParam('bairro')); 

    $perfilRepository = $entityManager->getRepository('App\Models\Entity\Perfil');
    $perfil = $perfilRepository->find($request->getParam('perfil'));
    
    $atividadeRepository = $entityManager->getRepository('App\Models\Entity\AtividadeProfissional');
    $atividade_principal = $atividadeRepository->find($request->getParam('atividade_principal'));
    $extra = $atividadeRepository->find($request->getParam('extra'));

    $user->setNome($request->getParam('nome'))
        ->setNome_Fantasia($request->getParam('fantasia'))
        ->setPassword($request->getParam('password'))
        ->setCep($request->getParam('cep'))
        ->setEndereco($request->getParam('endereco'))
        ->setComplemento($request->getParam('complemento'))
        ->setBairro($bairro)
        ->setEmail($request->getParam('email'))
        ->setTelefone1($request->getParam('telefone1'))
        ->setTelefone2($request->getParam('telefone2'))
        ->setTelefone3($request->getParam('telefone3'))
        ->setTelefone4($request->getParam('telefone4'))
        ->setCpf($request->getParam('cpf'))
        ->setCnpj($request->getParam('cnpj'))
        ->setImagem($request->getParam('foto'))
        ->setFrenterg($request->getParam('frenterg'))
        ->setVersorg($request->getParam('versorg'))
        ->setComprovante($request->getParam('comprovante'))
        ->setAtividade_Principal($atividade_principal)
        ->setAtividade_Extra($extra)
        ->setSituacao_Cadastral($request->getParam('situacao_cadastral'))
        ->setPerfil($perfil);

    /**
     * Persiste a entidade no banco de dados
     */
    $entityManager->persist($user);
    $entityManager->flush();        

    
    $return = $response->withJson($user, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});


$app->put('/api/alterarSenha/{id}', function (Request $request, Response $response) use ($app,$entityManager) {

    /**
     * Pega o ID do Usuario informado na URL
     */
    $route = $request->getAttribute('route');
    $id = $route->getArgument('id');
    $usersRepository = null;
    if($request->getParam('perfil') == "cliente"){
        $usersRepository = $entityManager->getRepository('App\Models\Entity\Usuario');
    }elseif($request->getParam('perfil') == "profissional"){
        $usersRepository = $entityManager->getRepository('App\Models\Entity\Profissional');
    }
    /**
     * Encontra o Usuario no Banco
     */ 
    $user = $usersRepository->find($id);   

    /**
     * Atualiza e Persiste o Usuario com os parâmetros recebidos no request
     */
    $user->setPassword($request->getParam('password'));

    /**
     * Persiste a entidade no banco de dados
     */
    $entityManager->persist($user);
    $entityManager->flush();        

    
    $return = $response->withJson($user, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

/**
 * Deleta o Usuario informado pelo ID
 * @request curl -X DELETE http://localhost:8000/user/3
 */
$app->delete('/api/usuario/{id}', function (Request $request, Response $response) use ($app,$entityManager) {
    /**
     * Pega o ID do Usuario informado na URL
     */
    $route = $request->getAttribute('route');
    $id = $route->getArgument('id');

    /**
     * Encontra o Usuario no Banco
     */ 
    
    $usersRepository = $entityManager->getRepository('App\Models\Entity\Usuario');
    $user = $usersRepository->find($id);   

    /**
     * Remove a entidade
     */
    $entityManager->remove($user);
    $entityManager->flush(); 

    $return = $response->withJson(['msg' => "Deletando o Usuario {$id}"], 204)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

$app->post('/api/login', function (Request $request, Response $response) use ($app,$entityManager) {
    
        $params = (object) $request->getParams();
        $usersRepository = null;
        if($params->perfil == "cliente"){
            $usersRepository = $entityManager->getRepository('App\Models\Entity\Usuario');
        }elseif($params->perfil == "profissional"){
            $usersRepository = $entityManager->getRepository('App\Models\Entity\Profissional');
        }
        $userBanco = $usersRepository->findOneBy(array('email' => $params->email));
      
        if(password_verify($params->password, $userBanco->getPassword())){
            $token = ['usuario'=> $userBanco , 'token' => 'Bearer $2y$10$AJi4JsNTXkoJar.RvC0r9eEXnvPtUKA74h1UqPAujPac8RChKmvb6' ];
            return $response->withJson($token, 200)
            ->withHeader('Content-type', 'application/json');
        } 
        return $response->withStatus(401, 'Usuario ou senha incorretos');
        
    });

    $app->post('/api/validaSemSenha', function (Request $request, Response $response) use ($app,$entityManager) {
    
        $params = (object) $request->getParams();
        $usersRepository = null;
        if($params->perfil == "cliente"){
            $usersRepository = $entityManager->getRepository('App\Models\Entity\Usuario');
        }elseif($params->perfil == "profissional"){
            $usersRepository = $entityManager->getRepository('App\Models\Entity\Profissional');
        }
        $userBanco = $usersRepository->findOneBy(array('email' => $params->email,'cpf' => $params->cpf,'telefone1' => $params->telefone1));
      
        if($userBanco != null){
           return $response->withJson($userBanco, 200)
            ->withHeader('Content-type', 'application/json');
        } 
        return $response->withStatus(404, 'Usuario não encontrado');
        
    });

    $app->post('/api/perfil', function (Request $request, Response $response) use ($app,$entityManager) {
        
            $params = (object) $request->getParams();
            $perfil = new Perfil($params->nome);
            
            $entityManager->persist($perfil);
            $entityManager->flush();
               
            $return = $response->withJson($perfil, 201)
                ->withHeader('Content-type', 'application/json');
            return $return;
        });
    
        $app->get('/api/perfil/{id}', function (Request $request, Response $response) use ($app,$entityManager) {
            $route = $request->getAttribute('route');
            $id = $route->getArgument('id');
            $perfilRepository = $entityManager->getRepository('App\Models\Entity\Perfil');
            $perfil = $perfilRepository->find($id);        
        
            $return = $response->withJson($perfil, 200)
                ->withHeader('Content-type', 'application/json');
            return $return;
        });
       
        $app->get('/api/perfil', function (Request $request, Response $response) use ($app,$entityManager) {
        
            $perfilRepository = $entityManager->getRepository('App\Models\Entity\Perfil');
            $perfil = $perfilRepository->findAll();
        
            $return = $response->withJson($perfil, 200)
                ->withHeader('Content-type', 'application/json');
            return $return;
        });
    
        $app->post('/api/atividade', function (Request $request, Response $response) use ($app,$entityManager) {
        
            $params = (object) $request->getParams();
            $atividade = new AtividadeProfissional($params->nome);
            
            $entityManager->persist($atividade);
            $entityManager->flush();
               
            $return = $response->withJson($atividade, 201)
                ->withHeader('Content-type', 'application/json');
            return $return;
        });
    
    $app->get('/api/consulta/atividade', function (Request $request, Response $response) use ($app,$entityManager) {
        
            $atividadeRepository = $entityManager->getRepository('App\Models\Entity\AtividadeProfissional');
            $atividade = $atividadeRepository->findAll();
        
            $return = $response->withJson($atividade, 200)
                ->withHeader('Content-type', 'application/json');
            return $return;
        });

    $app->get('/api/atividade/{id}', function (Request $request, Response $response) use ($app,$entityManager) {
            $route = $request->getAttribute('route');
            $id = $route->getArgument('id');
            $atividadeRepository = $entityManager->getRepository('App\Models\Entity\AtividadeProfissional');
            $atividade = $atividadeRepository->find($id);        
        
            $return = $response->withJson($atividade, 200)
                ->withHeader('Content-type', 'application/json');
            return $return;
        });
    
        $app->get('/api/consulta/bairro', function (Request $request, Response $response) use ($app,$entityManager) {
        
            $bairroRepository = $entityManager->getRepository('App\Models\Entity\Bairro');
            $bairros = $bairroRepository->findAll();
        
            $return = $response->withJson($bairros, 200)
                ->withHeader('Content-type', 'application/json');
            return $return;
        });

    $app->get('/api/bairro/{id}', function (Request $request, Response $response) use ($app,$entityManager) {
            $route = $request->getAttribute('route');
            $id = $route->getArgument('id');
            $bairroRepository = $entityManager->getRepository('App\Models\Entity\Bairro');
            $bairro = $bairroRepository->find($id);        
        
            $return = $response->withJson($bairro, 200)
                ->withHeader('Content-type', 'application/json');
            return $return;
        });

$app->post('/api/consulta/quantidade/profissional/bairro/atividade', function (Request $request, Response $response) use ($app,$entityManager) {
    $params = (object) $request->getParams();
    $repository = $entityManager->getRepository('App\Models\Entity\Profissional');
    
    $bairroRepository = $entityManager->getRepository('App\Models\Entity\Bairro');
    $bairro = $bairroRepository->find($params->id_bairro);
    
    $atividadeRepository = $entityManager->getRepository('App\Models\Entity\AtividadeProfissional');
    $atividade = $atividadeRepository->find($params->id_atividade); 

    $totalPorBairro =  $repository->createQueryBuilder('u')
            ->select('count(u.id)')
            ->andWhere('u.bairro = :bairro')
            ->setParameter('bairro', $bairro)
            ->andWhere('u.atividade_principal = :atividade')
            ->setParameter('atividade', $atividade)
            ->getQuery()
            ->getSingleScalarResult();
    
    $totalPorAtividade =  $repository->createQueryBuilder('u')
    ->select('count(u.id)')
    ->andWhere('u.atividade_principal = :atividade')
    ->setParameter('atividade', $atividade)
    ->getQuery()
    ->getSingleScalarResult();

    $resultado = [
        'totalPorBairro'=>$totalPorBairro,
        'totalPorAtividade'=>$totalPorAtividade
    ];
    $return = $response->withJson($resultado,200)
                ->withHeader('Content-type', 'application/json');
            return $return;
        });

$app->get('/api/consulta/horario', function (Request $request, Response $response) use ($app,$entityManager) {
        
    $repository = $entityManager->getRepository('App\Models\Entity\HorarioServico');
    $horarios = $repository->findAll();
        
    $return = $response->withJson($horarios, 200)
        ->withHeader('Content-type', 'application/json');
            return $return;
});

$app->post('/api/solicitar/orcamento', function (Request $request, Response $response) use ($app,$entityManager) {
        
    $params = (object) $request->getParams();
    
    $usersRepository = $entityManager->getRepository('App\Models\Entity\Usuario');
    $usuario = $usersRepository->find($params->idUsuario);   

    $atividadeRepository = $entityManager->getRepository('App\Models\Entity\AtividadeProfissional');
    $atividade = $atividadeRepository->find($params->idAtividade); 

    $bairroRepository = $entityManager->getRepository('App\Models\Entity\Bairro');
    $bairro = $bairroRepository->find($params->idBairro); 

    $horarioRepository = $entityManager->getRepository('App\Models\Entity\HorarioServico');
    $horario = $horarioRepository->find($params->idHorario);
    
    $localAtendimentoRepository = $entityManager->getRepository('App\Models\Entity\LocalAtendimento');
    $localAtendimento = $localAtendimentoRepository->find($params->idLocalAtendimento);

    $urgenciaServicoRepository = $entityManager->getRepository('App\Models\Entity\UrgenciaServico');
    $urgenciaServico = $urgenciaServicoRepository->find($params->idUrgencia);

    $solicitacao = new SolicitacaoOrcamento($usuario, $atividade, $bairro, $params->textoSolicitacao, $urgenciaServico, $localAtendimento, $horario);
    
    $entityManager->persist($solicitacao);
    $entityManager->flush();
       
    $return = $response->withJson($solicitacao, 201)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

$app->run();