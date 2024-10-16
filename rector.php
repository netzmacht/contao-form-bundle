<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;

return RectorConfig::configure()
    ->withRootFiles()
    ->withRules([
        DeclareStrictTypesRector::class,
    ])
    ->withPreparedSets(
        //deadCode: true,
        //codeQuality: true,
        //typeDeclarations: true,
        //typeDeclarations: true,
        //privatization: true,
        //instanceOf: true,
        //strictBooleans: true,
    );
