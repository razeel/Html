<?php
declare(strict_types=1);

namespace Mammalia\Html\Ast;

use Mammalia\Html\Ast\Element;
use Mammalia\Html\Ast\Text;
use Mammalia\Html\Serializer\Element as Serializer;

class NormalElement extends Element implements Serializer
{

    protected $childNodes;

    public function __construct(string $localName, array $attributes, array $childNodes)
    {
        parent::__construct($localName, $attributes);
        $this->childNodes = $childNodes;
    }

    public function beautify(int $level = 0) : Element
    {
        $indentChild = new Text("\n" . str_repeat("    ", $level + 1));
        $indentClosingTag = new Text("\n" . str_repeat("    ", $level));
        $children = array_merge(array_reduce($this->childNodes, function ($children, $child) use ($level, $indentChild) {
            return array_merge($children, [$indentChild, $child->beautify($level + 1)]);
        }, []), [$indentClosingTag]);
        return new NormalElement($this->localName, $this->attributes, $children);
    }

    public function toHtml() : string
    {
        $htmlLocalName = $this->localName;
        $htmlAttributes = $this->attributesToHtml();
        $htmlChildeNodes = $this->childNodesToHtml();
        return "<{$htmlLocalName}{$htmlAttributes}>$htmlChildeNodes</$htmlLocalName>";
    }

    protected function childNodesToHtml()
    {
        return array_reduce($this->childNodes, function ($html, $childNode) {
            return $html . $childNode->toHtml();
        }, '');
    }
}
