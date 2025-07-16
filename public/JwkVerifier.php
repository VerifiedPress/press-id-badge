<?php
/**
 * The JwkVerifier is an independent JWK verifier that does not use openssl_* functions, as the
 * reconstruction of the public key breaks the openssl_* framework.
 * 
 * See ../tools/key2jwk for creating the proper JWK from a public key which includes the kid attribute
 * use to set the decentralize identification (DiD)
 */
class JwkVerifier
{
    protected string $message;
    protected string $signatureHex;
    protected array $jwk;

    public function __construct(array $jwk, string $message, string $signatureHex) {
        $this->jwk = $jwk;
        $this->message = mb_convert_encoding($message, 'UTF-8');
        $this->signatureHex = $signatureHex;
    }

    public function verify(): bool {
        // Decode inputs
        $modulus = self::base64url_decode($this->jwk['n']);
        $exponent = self::base64url_decode($this->jwk['e']);
        $signature = hex2bin($this->signatureHex);
        if ($signature === false) throw new Exception("Invalid hex signature");

        // Convert to big integers
        $n = self::binToBigInt($modulus);
        $e = self::binToBigInt($exponent);
        $s = self::binToBigInt($signature);

        // Perform RSA decryption: m = s^e mod n
        $m = bcpowmod($s, $e, $n);
        if ($m === null) return false;

        // Convert message to SHA-256 hash
        $expectedHash = hash('sha256', $this->message, true);

        // Extract hash from decrypted message
        $decryptedBin = self::bigIntToBin($m, strlen($modulus));
        $hashFromSig = substr($decryptedBin, -32);

        return hash_equals($expectedHash, $hashFromSig);
    }

    protected static function base64url_decode(string $data): string {
        $pad = strlen($data) % 4;
        if ($pad > 0) $data .= str_repeat('=', 4 - $pad);
        return base64_decode(strtr($data, '-_', '+/'));
    }

    protected static function binToBigInt(string $bin): string {
        $hex = bin2hex($bin);
        return self::bcHexDec($hex);
    }

    protected static function bigIntToBin(string $int, int $length): string {
        $hex = self::bcDecHex($int);
        $hex = str_pad($hex, $length * 2, '0', STR_PAD_LEFT);
        return hex2bin($hex);
    }

    // Convert hex → decimal string
    protected static function bcHexDec(string $hex): string {
        $dec = '0';
        $len = strlen($hex);
        for ($i = 0; $i < $len; $i++) {
            $dec = bcmul($dec, '16');
            $dec = bcadd($dec, hexdec($hex[$i]));
        }
        return $dec;
    }

    // Convert decimal string → hex
    protected static function bcDecHex(string $dec): string {
        $hex = '';
        do {
            $remainder = bcmod($dec, '16');
            $hex = dechex((int)$remainder) . $hex;
            $dec = bcdiv($dec, '16', 0);
        } while (bccomp($dec, '0') > 0);
        return $hex;
    }
}
?>