<?php

declare(strict_types=1);

namespace App\Tests\Support;

use Jackalope\RepositoryFactoryJackrabbit;
use Jackalope\Session;
use PHPCR\NodeInterface;
use PHPCR\PropertyType;
use PHPCR\SessionInterface;
use PHPCR\SimpleCredentials;

/**
 * Inherited Methods
 * @method void wantTo($text)
 * @method void wantToTest($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 *
 * @SuppressWarnings(PHPMD)
*/
class UnitTester extends \Codeception\Actor
{
    public const PROPERTY_MIME_TYPE = 'jcr:mimeType';
    public const PROPERTY_FILE = 'jcr:data';

    public const NODE_NAME_CONTENT = 'jcr:content';

    public const TYPE_CONTENT_FILE = 'nt:file';
    public const TYPE_CONTENT_RESOURCE = 'nt:resource';

    protected SessionInterface $session;

    use _generated\UnitTesterActions;

    public function getPhpcrSession(bool $force = false): SessionInterface
    {
        if (!isset($this->session) || $force) {
            $jackrabbit_url = $_ENV['JACKRABBIT_URL'];
            $user           = $_ENV['JACKRABBIT_USER'];
            $pass           = $_ENV['JACKRABBIT_PASS'];
            $workspace      = $_ENV['JACKRABBIT_WORKSPACE'];

            $factory = new RepositoryFactoryJackrabbit();
            $repository = $factory->getRepository(
                array("jackalope.jackrabbit_uri" => $jackrabbit_url)
            );
            $credentials = new SimpleCredentials($user, $pass);
            $this->session = $repository->login($credentials, $workspace);
        }

        return $this->session;
    }

    public function getTryFilePath(): string
    {
        return $_ENV['DATA_DIR'] . 'small.jpg';
    }

    /**
     * @param resource $file
     */
    public function newFile(string $name, $file): NodeInterface
    {
        $root = $this->getPhpcrSession()->getRootNode();
        $fileNode = $root->addNode($name, self::TYPE_CONTENT_FILE);
        $contentNode = $fileNode->addNode(self::NODE_NAME_CONTENT, self::TYPE_CONTENT_RESOURCE);
        $contentNode->setProperty(self::PROPERTY_FILE, $file, PropertyType::BINARY);
        $contentNode->setProperty(self::PROPERTY_MIME_TYPE, 'image/jpeg');

        return $fileNode;
    }

    public function getResource(string $path)
    {
        return $this->getPhpcrSession()
            ->getNode($path)
            ->getNode(self::NODE_NAME_CONTENT)
            ->getProperty(self::PROPERTY_FILE)
            ->getValue()
        ;
    }
}
