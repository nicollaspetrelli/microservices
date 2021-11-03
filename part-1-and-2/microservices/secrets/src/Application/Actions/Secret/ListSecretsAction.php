<?php
declare(strict_types=1);

namespace Secrets\Application\Actions\Secret;

use Secrets\Domain\Secret\Secret;
use Secrets\Domain\Secret\SecretRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Vcampitelli\Framework\Acl\AclClientInterface;
use Vcampitelli\Framework\Core\Application\Actions\AclAction;

class ListSecretsAction extends AclAction
{
    /**
     * @param LoggerInterface         $logger
     * @param AclClientInterface|null $acl
     * @param SecretRepository        $secretRepository
     */
    public function __construct(
        LoggerInterface $logger,
        ?AclClientInterface $acl,
        private SecretRepository $secretRepository
    ) {
        parent::__construct($logger, $acl);
    }

    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $secrets = $this->getAllowedResourcesFor(Secret::class, $this->secretRepository);

        $this->logger->info("Secrets list was viewed.");

        return $this->respondWithData($secrets);
    }
}
