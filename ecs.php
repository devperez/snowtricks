<?php

use PHP_CodeSniffer\Standards\Generic\Sniffs\Metrics\CyclomaticComplexitySniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Metrics\NestingLevelSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions\CamelCapsFunctionNameSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions\InterfaceNameSuffixSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions\UpperCaseConstantNameSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Strings\UnnecessaryStringConcatSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Classes\ClassDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Methods\CamelCapsMethodNameSniff;
use PHP_CodeSniffer\Standards\PSR12\Sniffs\Classes\ClosingBraceSniff;
use PHP_CodeSniffer\Standards\PSR12\Sniffs\Classes\OpeningBraceSpaceSniff;
use PHP_CodeSniffer\Standards\PSR12\Sniffs\ControlStructures\BooleanOperatorPlacementSniff;
use PHP_CodeSniffer\Standards\PSR12\Sniffs\ControlStructures\ControlStructureSpacingSniff;
use PHP_CodeSniffer\Standards\PSR12\Sniffs\Functions\ReturnTypeDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR12\Sniffs\Operators\OperatorSpacingSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures\ElseIfDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures\SwitchDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\EndFileNewlineSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Methods\FunctionClosingBraceSniff;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->sets([SetList::PSR_12]);
    $ecsConfig->rule(CamelCapsMethodNameSniff::class);
    $ecsConfig->rule(ClassDeclarationSniff::class);
    $ecsConfig->rule(OpeningBraceSpaceSniff::class);
    $ecsConfig->rule(ClosingBraceSniff::class);
    $ecsConfig->rule(BooleanOperatorPlacementSniff::class);
    $ecsConfig->rule(ControlStructureSpacingSniff::class);
    $ecsConfig->rule(ReturnTypeDeclarationSniff::class);
    $ecsConfig->rule(OperatorSpacingSniff::class);
    $ecsConfig->rule(ElseIfDeclarationSniff::class);
    $ecsConfig->rule(SwitchDeclarationSniff::class);
    $ecsConfig->rule(EndFileNewlineSniff::class);
    $ecsConfig->rule(FunctionClosingBraceSniff::class);
    $ecsConfig->rule(CyclomaticComplexitySniff::class);
    $ecsConfig->rule(NestingLevelSniff::class);
    $ecsConfig->rule(CamelCapsFunctionNameSniff::class);
    $ecsConfig->rule(InterfaceNameSuffixSniff::class);
    $ecsConfig->rule(UpperCaseConstantNameSniff::class);
    $ecsConfig->rule(UnnecessaryStringConcatSniff::class);

    $ecsConfig->ruleWithConfiguration(ArraySyntaxFixer::class, [
        'syntax' => 'short',
    ]);
};