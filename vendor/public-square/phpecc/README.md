## Pure PHP Elliptic Curve DSA and DH

[![Latest Stable Version](https://poser.pugx.org/public-square/phpecc/v/stable.png)](https://packagist.org/packages/public-square/phpecc)
[![Total Downloads](https://poser.pugx.org/public-square/phpecc/downloads.png)](https://packagist.org/packages/public-square/phpecc)
[![Latest Unstable Version](https://poser.pugx.org/public-square/phpecc/v/unstable.png)](https://packagist.org/packages/public-square/phpecc)
[![License](https://poser.pugx.org/public-square/phpecc/license.png)](https://packagist.org/packages/public-square/phpecc)

### Information

This library is a fork of Matyas Danter's ECC library. All credit goes to him and previous contributors.
This fork is a drop in replacement that contains support for Schnorr signing and verifying.

For more information on Elliptic Curve Cryptography please read [this fine article](http://www.matyasdanter.com/2010/12/elliptic-curve-php-oop-dsa-and-diffie-hellman/).

The library supports the following curves:

 - secp112r1
 - secp256k1
 - nistp192
 - nistp224
 - nistp256 / secp256r1
 - nistp384 / secp384r1
 - nistp521

During ECDSA, a random value `k` is required. It is acceptable to use a true RNG to generate this value, but
should the same `k` value ever be repeatedly used for a key, an attacker can recover that signing key.
The HMAC random generator can derive a deterministic k value from the message hash and private key, voiding
this concern.

The library uses a non-branching Montgomery ladder for scalar multiplication, as it's constant time and avoids secret
dependant branches.

### License

This package is released under the MIT license.

### Requirements

* PHP 8.0+
* composer
* ext-gmp

### Installation

You can install this library via Composer :

`composer require public-square/phpecc`

### Contribute

Please open a pull request.

### Usage

Examples:
 * [Key generation](./examples/key_generation.php)
 * [ECDH exchange](./examples/ecdh_exchange.php)
 * [Signature creation](./examples/creating_signature.php)
 * [Signature verification](./examples/verify_signature.php)
