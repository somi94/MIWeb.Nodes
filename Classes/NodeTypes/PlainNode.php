<?php
namespace MIWeb\Nodes\NodeTypes;


use MIWeb\Nodes\Domain\Model\Node;

class PlainNode extends AbstractNodeType {
	/**
	 * @param Node $node
	 * @return string
	 */
	public function render($node) {
		return $this->getAttributeValue('content', $node);
	}
}
