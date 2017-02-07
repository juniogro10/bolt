<?php

namespace Bolt\Configuration\Validation;

use Bolt\Configuration\LowlevelChecks;
use Bolt\Configuration\ResourceManager;
use Bolt\Exception\Configuration\Validation\System\ApacheValidationException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Apache .htaccess validation check.
 *
 * @author Gawain Lynch <gawain.lynch@gmail.com>
 */
class Apache implements ValidationInterface, ResourceManagerAwareInterface
{
    /** @var ResourceManager */
    private $resourceManager;

    /**
     * This check looks for the presence of the .htaccess file inside the web directory.
     * It is here only as a convenience check for users that install the basic version of Bolt.
     *
     * If you see this error and want to disable it, call $config->getVerifier()->disableApacheChecks();
     * inside your bootstrap.php file, just before the call to $config->verify().
     *
     * {@inheritdoc}
     */
    public function check()
    {
        $request = Request::createFromGlobals();
        $serverSoftware = $request->server->get('SERVER_SOFTWARE', '');
        $isApache = strpos($serverSoftware, 'Apache') !== false;
        /** @var LowlevelChecks $verifier */
        $verifier = $this->resourceManager->getVerifier();
        if ($verifier && $verifier->disableApacheChecks === true || !$isApache) {
            return;
        }

        $path = $this->resourceManager->getPath('web/.htaccess');
        if (is_readable($path)) {
            return;
        }

        throw new ApacheValidationException();
    }

    /**
     * {@inheritdoc}
     */
    public function setResourceManager(ResourceManager $resourceManager)
    {
        $this->resourceManager = $resourceManager;
    }
}
