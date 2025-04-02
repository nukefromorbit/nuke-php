<?php

namespace Nuke;

use phpmock\MockBuilder;
use phpmock\functions\FixedValueFunction;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Nuke\Exceptions\CryptEncryptException;
use Nuke\Exceptions\CryptDecryptException;
use Nuke\Exceptions\CryptInvalidKeyException;
use Nuke\Exceptions\CryptInvalidPayloadException;

#[CoversClass(Crypt::class)]
final class CryptTest extends TestCase
{
    private const NUKE_SECRET = 'GvFyG1eeaxyjVmFAWhCI6f8DZsYZch1rux3iFyBYgxU=';

    public function testEncryptCryptInvalidKeyException(): void
    {
        $this->expectException(CryptInvalidKeyException::class);
        Crypt::encrypt('my-secret-value', base64_encode(random_bytes(2)));
    }

    public function testDecryptCryptInvalidKeyException(): void
    {
        $this->expectException(CryptInvalidKeyException::class);
        $encrypt = Crypt::encrypt('my-secret-value', self::NUKE_SECRET);
        Crypt::decrypt($encrypt, base64_encode(random_bytes(2)));
    }

    public function testDecryptCryptInvalidPayloadException(): void
    {
        $this->expectException(CryptInvalidPayloadException::class);
        Crypt::decrypt('invalid', self::NUKE_SECRET);
    }

    public function testEncryptDecrypt(): void
    {
        $encrypt = Crypt::encrypt('my-secret-value', self::NUKE_SECRET);
        $decrypt = Crypt::decrypt($encrypt, self::NUKE_SECRET);
        $this->assertEquals('my-secret-value', $decrypt);
    }

    #[RunInSeparateProcess]
    public function testEncryptCryptOpensslEncryptException(): void
    {
        $builder = new MockBuilder();
        $builder->setNamespace(__NAMESPACE__)
            ->setName('openssl_encrypt')
            ->setFunctionProvider(new FixedValueFunction(false));
        $mock = $builder->build();

        $mock->enable();
        $this->expectException(CryptEncryptException::class);
        Crypt::encrypt('my-secret-value', self::NUKE_SECRET);
        $mock->disable();
    }

    #[RunInSeparateProcess]
    public function testEncryptJsonCryptEncryptException(): void
    {
        $builder = new MockBuilder();
        $builder->setNamespace(__NAMESPACE__)
            ->setName('json_last_error')
            ->setFunctionProvider(new FixedValueFunction(1));
        $mock = $builder->build();

        $mock->enable();
        $this->expectException(CryptEncryptException::class);
        Crypt::encrypt('my-secret-value', self::NUKE_SECRET);
        $mock->disable();
    }

    #[RunInSeparateProcess]
    public function testDecryptCryptMbStrlenEncryptException(): void
    {
        $builder = new MockBuilder();
        $builder->setNamespace(__NAMESPACE__)
            ->setName('hash_equals')
            ->setFunctionProvider(new FixedValueFunction(false));
        $mock = $builder->build();

        $mock->enable();
        $this->expectException(CryptDecryptException::class);
        $encrypt = Crypt::encrypt('my-secret-value', self::NUKE_SECRET);
        Crypt::decrypt($encrypt, self::NUKE_SECRET);
        $mock->disable();
    }

    #[RunInSeparateProcess]
    public function testDecryptOpensslCryptEncryptException(): void
    {
        $builder = new MockBuilder();
        $builder->setNamespace(__NAMESPACE__)
            ->setName('openssl_decrypt')
            ->setFunctionProvider(new FixedValueFunction(false));
        $mock = $builder->build();

        $mock->enable();
        $this->expectException(CryptDecryptException::class);
        $encrypt = Crypt::encrypt('my-secret-value', self::NUKE_SECRET);
        Crypt::decrypt($encrypt, self::NUKE_SECRET);
        $mock->disable();
    }
}
