--TEST--
Double type: -1.0
--DESCRIPTION--
Generated by scripts/convert-bson-corpus-tests.php

DO NOT EDIT THIS FILE
--FILE--
<?php

require_once __DIR__ . '/../utils/tools.php';

$canonicalBson = hex2bin('10000000016400000000000000F0BF00');
$canonicalExtJson = '{"d" : {"$numberDouble": "-1.0"}}';
$relaxedExtJson = '{"d" : -1.0}';

// Canonical BSON -> Native -> Canonical BSON 
echo bin2hex(fromPHP(toPHP($canonicalBson))), "\n";

// Canonical BSON -> Canonical extJSON 
echo json_canonicalize(toCanonicalExtendedJSON($canonicalBson)), "\n";

// Canonical BSON -> Relaxed extJSON 
echo json_canonicalize(toRelaxedExtendedJSON($canonicalBson)), "\n";

// Canonical extJSON -> Canonical BSON 
echo bin2hex(fromJSON($canonicalExtJson)), "\n";

// Relaxed extJSON -> BSON -> Relaxed extJSON 
echo json_canonicalize(toRelaxedExtendedJSON(fromJSON($relaxedExtJson))), "\n";

?>
===DONE===
<?php exit(0); ?>
--EXPECT--
10000000016400000000000000f0bf00
{"d":{"$numberDouble":"-1.0"}}
{"d":-1}
10000000016400000000000000f0bf00
{"d":-1}
===DONE===