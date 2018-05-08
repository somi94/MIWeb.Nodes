<?php
namespace MIWeb\Nodes\NodeTypes;



use MIWeb\Nodes\Domain\Model\Node;

interface NodeTypeInterface {
	/**
	 * @param Node $node
	 * @return string
	 */
	public function render($node);

	/**
	 * @param Iterable<Node> $nodes
	 * @param int $page
	 * @param int $numPages
	 * @return string
	 */
	public function renderList($nodes, $page = 0, $numPages = 0);

	/**
	 * @return string
	 */
	public function getIdentifier();

	/**
	 * @return array
	 */
	public function getConfig();

	/**
	 * @return array
	 */
	public function getMetaAttributes();
}
