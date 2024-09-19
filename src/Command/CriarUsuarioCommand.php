<?php

namespace App\Command;

use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:criar-usuario',
)]
class CriarUsuarioCommand extends Command
{
    public function __construct(
        private UsuarioRepository $usuarioRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
        ->addArgument('email', InputArgument::REQUIRED)
        ->addArgument('senha', InputArgument::REQUIRED);

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $senha = $input->getArgument('senha');

        if (empty($email) || empty($senha)) {
            throw new \InvalidArgumentException('Informe o e-mail e senha por favor!!');
        }
   

    $usuario = new Usuario();
    $usuario
    ->setEmail($email)
    ->setPassword($this->userPasswordHasher->hashPassword($usuario, $senha))
    ->setRoles(['ROLE_USER']);
    
    $this->usuarioRepository->salvar($usuario);

    return Command::SUCCESS;
}
}