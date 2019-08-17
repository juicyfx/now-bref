--TEST--
Decimal128: [decq607] fold-down full sequence (Clamped)
--DESCRIPTION--
Generated by scripts/convert-bson-corpus-tests.php

DO NOT EDIT THIS FILE
--FILE--
<?php

require_once __DIR__ . '/../utils/tools.php';

$canonicalBson = hex2bin('1800000013640000000040EAED7446D09C2C9F0C00FE5F00');
$canonicalExtJson = '{"d" : {"$numberDecimal" : "1.000000000000000000000000000000E+6141"}}';
$degenerateExtJson = '{"d" : {"$numberDecimal" : "1E+6141"}}';

// Canonical BSON -> Native -> Canonical BSON 
echo bin2hex(fromPHP(toPHP($canonicalBson))), "\n";

// Canonical BSON -> Canonical extJSON 
echo json_canonicalize(toCanonicalExtendedJSON($canonicalBson)), "\n";

// Canonical extJSON -> Canonical BSON 
echo bin2hex(fromJSON($canonicalExtJson)), "\n";

// Degenerate extJSON -> Canonical BSON 
echo bin2hex(fromJSON($degenerateExtJson)), "\n";

?>
===DONE===
<?php exit(0); ?>
--EXPECT--
1800000013640000000040eaed7446d09c2c9f0c00fe5f00
{"d":{"$numberDecimal":"1.000000000000000000000000000000E+6141"}}
1800000013640000000040eaed7446d09c2c9f0c00fe5f00
1800000013640000000040eaed7446d09c2c9f0c00fe5f00
===DONE===