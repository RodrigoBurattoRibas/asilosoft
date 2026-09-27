<?php


require_once dirname(__DIR__) . '/Session/AsiloSession.php';
require_once dirname(__DIR__, 2) . '/infra/Database/Database.php';
require_once dirname(__DIR__, 2) . '/modules/usuarios/repository/AsiloUsuarioRepository.php';
require_once dirname(__DIR__, 2) . '/modules/usuarios/repository/AsiloRepositorioUsuariosSessao.php';
require_once dirname(__DIR__, 2) . '/modules/idosos/repository/AsiloIdosoRepository.php';
require_once dirname(__DIR__, 2) . '/modules/idosos/repository/AsiloQuartoRepository.php';
require_once dirname(__DIR__, 2) . '/modules/idosos/repository/AsiloRepositorioIdososSessao.php';

final class AsiloRuntime
{
    private AsiloSession $session;
    private object $userRepository;
    private object $residentRepository;
    private object $roomRepository;
    private bool $demoMode = false;

    public function __construct()
    {
        $this->session = new AsiloSession();

        try {
            $pdo = PDOMysqli::connection();
            $this->userRepository = new AsiloUsuarioRepository($pdo);
            $this->residentRepository = new AsiloIdosoRepository($pdo);
            $this->roomRepository = new AsiloQuartoRepository($pdo);
        } catch (PDOException) {
            $this->demoMode = true;
            $this->userRepository = new AsiloRepositorioUsuariosSessao($this->session);
            $this->residentRepository = new AsiloRepositorioIdososSessao($this->session);
            $this->roomRepository = $this->residentRepository;
        }
    }

    public function session(): AsiloSession
    {
        return $this->session;
    }

    public function userRepository(): object
    {
        return $this->userRepository;
    }

    public function residentRepository(): object
    {
        return $this->residentRepository;
    }

    public function roomRepository(): object
    {
        return $this->roomRepository;
    }

    public function demoMode(): bool
    {
        return $this->demoMode;
    }
}
