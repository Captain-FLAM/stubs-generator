<?php
declare(strict_types=1);
namespace StubsGenerator;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\UnionType;
use PhpParser\Node\IntersectionType;
use PhpParser\NodeVisitorAbstract;

/**
 * Adds a @return annotation when a function or method has a return type.
 */
class ReturnTypeCopyVisitor extends NodeVisitorAbstract
{
    public function enterNode(Node $node)
    {
        if ($node instanceof FunctionLike && $node->getReturnType()) {
            $doc = $node->getDocComment();
            if ($doc && strpos($doc->getText(), '@return') !== false) {
                return null;
            }

            $typeString = $this->formatType($node->getReturnType());
            $commentLines = $doc ? preg_split('/\r?\n/', $doc->getText()) : ['/**', ' */'];
            $end = array_pop($commentLines);
            if (trim($end) !== '*/') {
                $commentLines[] = $end;
            }
            $commentLines[] = ' * @return ' . $typeString;
            $commentLines[] = ' */';
            $node->setDocComment(new Doc(implode("\n", $commentLines)));
        }

        return null;
    }

    private function formatType($type): string
    {
        if ($type instanceof NullableType) {
            return '?' . $this->formatType($type->type);
        }
        if ($type instanceof UnionType) {
            $parts = [];
            foreach ($type->types as $t) {
                $parts[] = $this->formatType($t);
            }
            return implode('|', $parts);
        }
        if ($type instanceof IntersectionType) {
            $parts = [];
            foreach ($type->types as $t) {
                $parts[] = $this->formatType($t);
            }
            return implode('&', $parts);
        }
        if ($type instanceof Name) {
            return '\\' . ltrim($type->toString(), '\\');
        }
        if ($type instanceof Identifier) {
            return $type->name;
        }
        return '';
    }
}
