<?php

declare(strict_types=1);

namespace Guuzen\PsalmEnumPlugin;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use Psalm\CodeLocation;
use Psalm\Internal\Analyzer\ClassLikeAnalyzer;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterMethodCallAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterMethodCallAnalysisEvent;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use Psalm\Type\Atomic\TLiteralInt;
use Psalm\Type\Atomic\TLiteralString;

final class Plugin implements PluginEntryPointInterface, AfterMethodCallAnalysisInterface
{
    #[\Override]
    public function __invoke(RegistrationInterface $registration, ?\SimpleXMLElement $config = null): void
    {
        $registration->registerHooksFromClass(self::class);
    }

    #[\Override]
    public static function afterMethodCallAnalysis(AfterMethodCallAnalysisEvent $event): void
    {
        $expr = $event->getExpr();

        if (!$expr instanceof StaticCall || !$expr->class instanceof \PhpParser\Node\Name) {
            return;
        }

        if (!$expr->name instanceof Identifier) {
            return;
        }

        if ($expr->name->name !== 'from' || count($expr->args) < 1) {
            return;
        }

        $firstArg = $expr->args[0];

        if (!$firstArg instanceof Arg) {
            return;
        }

        $argType = $event->getStatementsSource()->getNodeTypeProvider()->getType($firstArg->value);

        if (!$argType) {
            return;
        }

        foreach ($argType->getAtomicTypes() as $type) {
            if (!$type instanceof TLiteralString && !$type instanceof TLiteralInt) {
                return;
            }

            $calledClass = (string)$expr->class;

            if ($calledClass === 'self') {
                $enumClass = $event->getContext()->self;
            } else {
                /**
                 * @psalm-suppress InternalClass
                 * @psalm-suppress InternalMethod
                 */
                $enumClass = ClassLikeAnalyzer::getFQCLNFromNameObject(
                    $expr->class,
                    $event->getStatementsSource()->getAliases(),
                );
            }

            if ($enumClass === null || !is_a($enumClass, \BackedEnum::class, true)) {
                return;
            }

            $values = array_map(
                fn (\BackedEnum $case) => $case->value,
                $enumClass::cases(),
            );

            if (!in_array($type->value, $values, true)) {
                IssueBuffer::accepts(
                    e: new InvalidEnumValue(
                        message: "Invalid value '{$type->value}' passed to {$enumClass}::from()",
                        code_location: new CodeLocation($event->getStatementsSource(), $expr),
                    ),
                    suppressed_issues: $event->getStatementsSource()->getSuppressedIssues(),
                );
            }
        }
    }
}
