<?php

use Phan\Issue;

return [
    'minimum_severity' => Issue::SEVERITY_LOW,
    "target_php_version" => '7.4',
    'suppress_issue_types' => [
        'PhanPluginNoCommentOnFunction',
        'PhanPluginNoCommentOnClass',
        'PhanPluginDescriptionlessCommentOnPublicMethod',
        'PhanPluginPossiblyStaticPublicMethod',
        'PhanPluginDescriptionlessCommentOnFunction',
        'PhanPluginNoCommentOnPrivateProperty',
        'PhanPluginNoCommentOnPublicMethod',
        'PhanPluginNoCommentOnPrivateMethod',
    ],
    'whitelist_issue_types' => [],
    'directory_list' => [
        'bin',
        'src',
        'test',
        'vendor',
    ],
    'analyzed_file_extensions' => ['php'],
    'exclude_analysis_directory_list' => [
        'vendor/'
    ],
    'plugins' => [
        'AlwaysReturnPlugin',
        'DollarDollarPlugin',
        'UnreachableCodePlugin',
        'DuplicateArrayKeyPlugin',
        'PregRegexCheckerPlugin',
        'PrintfCheckerPlugin',
        'PHPUnitAssertionPlugin',
        'UseReturnValuePlugin',
        'UnknownElementTypePlugin',
        'DuplicateExpressionPlugin',
        'WhitespacePlugin',
        'InlineHTMLPlugin',
        'NoAssertPlugin',
        'PossiblyStaticMethodPlugin',
        'HasPHPDocPlugin',
        'PHPDocToRealTypesPlugin',
        'PHPDocRedundantPlugin',
        'PreferNamespaceUsePlugin',
        'EmptyStatementListPlugin',
        'LoopVariableReusePlugin',
        'RedundantAssignmentPlugin',
        'StrictComparisonPlugin',
        'StrictLiteralComparisonPlugin',
        'ShortArrayPlugin',
        'SimplifyExpressionPlugin',
        'RemoveDebugStatementPlugin',
        'UnsafeCodePlugin',
        'DeprecateAliasPlugin',
    ],
];