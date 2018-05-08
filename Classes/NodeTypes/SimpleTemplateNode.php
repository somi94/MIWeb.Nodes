<?php
namespace MIWeb\Nodes\NodeTypes;


use MIWeb\Nodes\Domain\Model\Node;
use Neos\Utility\ObjectAccess;

class SimpleTemplateNode extends AbstractNodeType {
	/**
	 * @param Node $node
	 * @return string
	 */
	public function render($node) {
		$source = $this->getAttributeValue('templateSource', $node);
		$templatePathAndFilename = $this->getAttributeValue('templatePathAndFilename', $node);
		if ($templatePathAndFilename !== null) {
			$source = file_get_contents($templatePathAndFilename);
		}

		//return $source;

		return preg_replace_callback('/\{([a-zA-Z0-9\-_.]+)\}/', function ($matches) use ($node) {
			return ObjectAccess::getPropertyPath($node, $matches[1]);
		}, $source);
	}
}
