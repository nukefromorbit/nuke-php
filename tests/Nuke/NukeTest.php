<?php

namespace Nuke;

use Nuke\Exceptions\InvalidIdentifierException;
use Nuke\Exceptions\InvalidSignatureException;
use Nuke\Exceptions\MissingIdentifierException;
use Nuke\Exceptions\MissingSecretException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Nuke::class)]
#[UsesClass(Crypt::class)]
final class NukeTest extends TestCase
{
    private const NUKE_IDENTIFIER = '6lMyeWiyWZYeA1hBbtrtgGNQXMKbD9fV';
    private const NUKE_SECRET = 'GvFyG1eeaxyjVmFAWhCI6f8DZsYZch1rux3iFyBYgxU=';

    public function testGetSignatureMissingSecretException(): void
    {
        $this->expectException(MissingSecretException::class);
        Nuke::setIdentifier(self::NUKE_IDENTIFIER);
        Nuke::setSecret(null);
        Nuke::getSignature(time(), ['payload']);
    }

    public function testGetSignatureInvalidSignatureException(): void
    {
        Nuke::setIdentifier(self::NUKE_IDENTIFIER);
        Nuke::setSecret(self::NUKE_SECRET);
        $signature = Nuke::getSignature(time(), ['payload']);
        $this->assertMatchesRegularExpression('/^t=(\d+), v=([0-9a-zA-Z]+)$/', $signature);
        $this->expectException(InvalidSignatureException::class);
        Nuke::verifyIdentifierAndSignature(self::NUKE_IDENTIFIER, $signature, ['payload-different']);
    }

    public function testGetSignature(): void
    {
        Nuke::setIdentifier(self::NUKE_IDENTIFIER);
        Nuke::setSecret(self::NUKE_SECRET);
        $signature = Nuke::getSignature(time(), ['payload']);
        $this->assertMatchesRegularExpression('/^t=(\d+), v=([0-9a-zA-Z]+)$/', $signature);
        Nuke::verifyIdentifierAndSignature(self::NUKE_IDENTIFIER, $signature, ['payload']);
    }

    public function testVerifyIdentifierAndSignatureMissingIdentifierException(): void
    {
        $this->expectException(MissingIdentifierException::class);
        Nuke::setIdentifier(null);
        Nuke::setSecret(self::NUKE_SECRET);
        Nuke::verifyIdentifierAndSignature('', '', ['payload']);
    }

    public function testVerifyIdentifierAndSignatureInvalidIdentifierException(): void
    {
        $this->expectException(InvalidIdentifierException::class);
        Nuke::setIdentifier(self::NUKE_IDENTIFIER);
        Nuke::setSecret(self::NUKE_SECRET);
        Nuke::verifyIdentifierAndSignature(self::NUKE_IDENTIFIER . '-different', '', ['payload']);
    }

    public function testVerifyIdentifierAndSignatureMissingSecretException(): void
    {
        $this->expectException(MissingSecretException::class);
        Nuke::setIdentifier(self::NUKE_IDENTIFIER);
        Nuke::setSecret(null);
        Nuke::verifyIdentifierAndSignature(self::NUKE_IDENTIFIER, '', ['payload']);
    }

    public function testEncryptDecrypt(): void
    {
        Nuke::setIdentifier(self::NUKE_IDENTIFIER);
        Nuke::setSecret(self::NUKE_SECRET);
        $encrypt = Nuke::encrypt('my-secret-value');
        $decrypt = Nuke::decrypt($encrypt);
        $this->assertEquals('my-secret-value', $decrypt);
    }
}
