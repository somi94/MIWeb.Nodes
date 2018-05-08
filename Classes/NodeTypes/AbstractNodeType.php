<?php
namespace MIWeb\Nodes\NodeTypes;


abstract class AbstractNodeType implements NodeTypeInterface {
	/**
	 * @var string
	 */
	protected $identifier;

	/**
	 * @var array
	 */
	protected $config;

	public function __construct($identifier,$config) {
		$this->identifier = $identifier;
		$this->config = $config;
	}

	public function getIdentifier() {
		return $this->identifier;
	}

	public function getConfig() {
		return $this->config;
	}

	public function getMetaAttributes() {
		return isset($this->config['metaAttributes']) ? $this->config['metaAttributes'] : [];
	}

	public function getAttributeValue($attribute,$node = null) {
		return $node && $node->getAttribute($attribute) ? $node->getAttribute($attribute) : (isset($this->config['defaults'][$attribute]) ? $this->config['defaults'][$attribute] : null);
	}

	/**
	 * @param Iterable<Node> $nodes
	 * @param int $page
	 * @param int $numPages
	 * @return string
	 */
	public function renderList($nodes, $page = 0, $numPages = 0) {
		$out = "";
		foreach($nodes as $node) {
			$out .= $this->render($node);
		}
		return $out;
	}
}
